<?php
/**
 * @author    Fernando Caraballo Ortiz <fernando.ortiz@reiss.com>
 * @date      31/07/2014
 * @copyright Copyright (c) Reiss Clothing Ltd.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Reiss\WishlistBundle\Behat;

use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Symfony2Extension\Context\KernelAwareContext;
use Sylius\Bundle\ResourceBundle\Behat\DefaultContext;
use Behat\Gherkin\Node\TableNode;
use Behat\Behat\Context\Exception;

/**
 * @author Fernando Caraballo Ortiz <fernando.ortiz@reiss.com>
 */
class WishlistContext extends DefaultContext implements Context, KernelAwareContext, SnippetAcceptingContext
{
    private $variantId;
    private $wishlistId;
    private $wishlist;
    private $variant;

    /**
     * @Given there are the following wishlists:
     */
    public function thereAreTheFollowingWishlists(TableNode $table)
    {
        foreach ($table->getHash() as $data) {
            $wishlist = $this->newWishlist(
                $data['name'],
                $data['email'],
                $data['primary_wishlist']
            );
            $this->getContainer()->get('reiss.entity_manager.wishlist')->persistAndFlush($wishlist);
        }
    }

    /**
     * @Given there are the following Wishlist items
     */
    public function thereAreTheFollowingWishlistItems(TableNode $table)
    {
        foreach ($table->getHash() as $data) {
            $wishlistItem = $this->newWishlistItem(
                $data['product_variant'],
                $data['wishlist'],
                $data['size']
            );
            $this->getContainer()->get('reiss.entity_manager.wishlist_item')->persistAndFlush($wishlistItem);
        }
    }

    /**
     * @Given there are customers:
     */
    public function thereAreCustomers(TableNode $table)
    {
        $userFactory     = $this->getContainer()->get('sylius.factory.user');
        $userManager     = $this->getContainer()->get('sylius.manager.user');
        $customerFactory = $this->getContainer()->get('sylius.factory.customer');

        foreach ($table->getHash() as $data) {
            $user = $userFactory->createNew();

            $customer = $customerFactory->createNew()
                ->setFirstName($data['firstName'])
                ->setEmail($data['email']);

            $user
                ->setPassword($data['password'])
                ->setEnabled(true)
                ->setCustomer($customer);

            $userManager->persist($user);
            $userManager->flush();
        }
    }

    /**
     * @Given I remove the wishlist :wishlistName
     */
    public function iRemoveTheWishlist($wishlistName)
    {
        $wishlistRepository = $this->getContainer()->get('reiss.repository.wishlist');
        $wishlistManager    = $this->getContainer()->get('reiss.entity_manager.wishlist');

        $wishlist = $wishlistRepository->findOneByName($wishlistName);
        $wishlistManager->removeAndFlush($wishlist);

        $wishlistManager->ensurePrimaryWishlistIfExists($wishlist->getCustomer());
    }

    /**
     * @Then The wishlist :wishlistName should be the primary one
     */
    public function theWishlistShouldBeThePrimaryOne($wishlistName)
    {
        $wishlistRepository = $this->getContainer()->get('reiss.repository.wishlist');

        $wishlist = $wishlistRepository->findOneByName($wishlistName);

        \PHPUnit_Framework_Assert::assertTrue($wishlist->getPrimary());
    }

    /**
     * @Then I should have the title :expectedName for :firstName
     */
    public function iShouldHaveTheTitleFor($expectedName, $firstName)
    {
        $wishlistHelper     = $this->getContainer()->get('reiss.wishlist_frontend.helper');
        $customerRepository = $this->getContainer()->get('sylius.repository.customer');

        $wishlistsName = $wishlistHelper->wishlistGroupName($customerRepository->findOneBy(array('firstName' => $firstName)));

        \PHPUnit_Framework_Assert::assertEquals($expectedName, $wishlistsName);
    }

    /**
     * @When I add a :arg1 with size :arg2 for :email
     */
    public function iAddAWithSize($productName, $sizeName, $email)
    {
        $variantRepository  = $this->getContainer()->get('sylius.repository.product_variant');
        $productRepository  = $this->getContainer()->get('sylius.repository.product');
        $wishlistManager    = $this->getContainer()->get('reiss.entity_manager.wishlist');
        $customerRepository = $this->getContainer()->get('sylius.repository.customer');
        $sizeRepository     = $this->getContainer()->get('reiss.repository.size');

        $product  = $productRepository->findOneBy(array('name' => $productName));
        $variant  = $variantRepository->findOneBy(array('object' => $product, 'size' => $sizeRepository->findOneByName($sizeName)));
        $customer = $customerRepository->findOneBy(array('email' => $email));

        $wishlistManager->addItemToPrimaryWishlist($customer, $variant);
    }

    /**
     * @Then I should have :expectedName in :wishlistName
     */
    public function iShouldHaveInFor($expectedName, $wishlistName)
    {
        $wishlistRepository     = $this->getContainer()->get('reiss.repository.wishlist');
        $wishlistItemRepository = $this->getContainer()->get('reiss.repository.wishlist_item');

        $wishlist     = $wishlistRepository->findOneByName($wishlistName);
        $wishlistItem = $wishlistItemRepository->findOneBy(array('wishlist' => $wishlist));

        $productName = $wishlistItem->getVariant()->getProduct()->getName();

        \PHPUnit_Framework_Assert::assertEquals($expectedName, $productName);
    }

    /**
     * @param string   $name
     * @param string   $customerEmail
     * @param Wishlist $primaryWishlist
     *
     * @return mixed
     */
    private function newWishlist($name, $customerEmail, $primaryWishlist)
    {
        $wishlistManager = $this->getContainer()->get('reiss.entity_manager.wishlist');

        $customerRepository = $this->getContainer()->get('sylius.repository.customer');
        $customer           = $customerRepository->findOneBy(array('email' => $customerEmail));

        $wishlist = $wishlistManager->createNew();
        $wishlist
            ->setCustomer($customer)
            ->setName($name)
            ->setPrimary($primaryWishlist);

        $this->wishlist = $wishlist;

        return $wishlist;
    }

    /**
     * @param $product
     * @param $wishlist
     * @param $size
     *
     * @return mixed
     */
    private function newWishlistItem($product, $wishlist, $size)
    {
        // TODO: this looks wrong presentation => size??
        $variants = $this->getContainer()->get('sylius.repository.product_variant')->findBy(array('presentation' => $size));

        foreach ($variants as $v) {
            if ($v->getProduct()->getName() == $product) {
                $variant = $v;
            }
        }

        $wishlistItemManager = $this->getContainer()->get('reiss.entity_manager.wishlist_item');

        $wishlistObject = $this->getContainer()->get('reiss.repository.wishlist')->findOneBy(array('name' => $wishlist));

        $wishlistItem = $wishlistItemManager->createNew();
        $wishlistItem
            ->setVariant($variant)
            ->setWishlist($wishlistObject);

        return $wishlistItem;
    }
}