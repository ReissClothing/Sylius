<?php
/**
 * @author    Gonzalo Vilaseca <gonzalo.vilaseca@reiss.com>
 * @date      05/06/15
 * @copyright Copyright (c) Reiss Clothing Ltd.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Reiss\UserBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Reiss\CoreBundle\Entity\Channel;
use Reiss\CustomerServicesBundle\Entity\Ticket;
use Reiss\StoreLocatorBundle\Entity\Store;
use Reiss\WishlistBundle\Entity\Wishlist;
use Sylius\Component\Core\Model\Customer as BaseCustomer;
use Sylius\Component\Sequence\Model\SequenceSubjectInterface;

/**
 * @author Gonzalo Vilaseca <gonzalo.vilaseca@reiss.com>
 */
class Customer extends BaseCustomer implements SequenceSubjectInterface
{
    const CUSTOMER_SEQUENCE_TYPE = 'customer';
    const CUSTOMER_SEQUENCE_OFFSET = 2000000;

    const CUSTOMER_ROLE_CUSTOMER_SERVICES_OPERATOR = 'reiss_customer_services_operator';
    const CUSTOMER_ROLE_CUSTOMER_SERVICES_MANAGER  = 'reiss_customer_services_manager';

    const CUSTOMER_SOURCE_DETAIL_CHECKOUT = 'Checkout';
    const CUSTOMER_SOURCE_DETAIL_COMPETITION = 'Competition';
    const CUSTOMER_SOURCE_DETAIL_PERSONAL_FORM = 'Personal Form';
    const CUSTOMER_SOURCE_DETAIL_REGISTER = 'Register';
    const CUSTOMER_SOURCE_DETAIL_SUBSCRIBE = 'Subscribe';

    /**
     * @var string
     */
    protected $phoneNumber;

    /**
     * Customer code for usage with external tools
     *
     * @var int
     */
    protected $number;

    /**
     * @var boolean
     */
    protected $emailOptIn = false;

    /**
     * @var Channel
     */
    protected $channel;

    /**
     * @var string
     */
    protected $source = 'Web';

    /**
     * @var string
     */
    protected $sourceDetail;

    /**
     * @var Store
     */
    protected $store;

    /**
     * @var \DateTime
     */
    protected $firstSubscribed;

    /**
     * The detected / specified country for customer.
     * This may seem a bit odd to store a country outside of an address
     * but many of our customer data sources only have a country, not a full address.
     * It's used for email newsletter segmentation
     *
     * @var string
     */
    protected $countryCode;

    /**
     * @var ArrayCollection
     */
    protected $tickets;

    /**
     * @var ArrayCollection
     */
    protected $subscriptions;

    /**
     * @var ArrayCollection|Wishlist[]
     */
    protected $wishlists;

    /**
     * @var Collection|CustomerNewsletterSubscriptionLog[]
     */
    protected $subscriptionLogs;

    /**
     * Construct
     */
    public function __construct()
    {
        parent::__construct();
        $this->tickets       = new ArrayCollection();
        $this->subscriptions = new ArrayCollection();
        $this->wishlists     = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function getPhoneNumber()
    {
        return $this->phoneNumber;
    }

    /**
     * @param string $phoneNumber
     *
     * @return Customer
     */
    public function setPhoneNumber($phoneNumber)
    {
        $this->phoneNumber = $phoneNumber;
    }

    /**
     * @param Ticket $ticket
     *
     * @return Customer
     */
    public function addTicket(Ticket $ticket)
    {
        if (!$this->hasTicket($ticket)) {
            $this->tickets[] = $ticket;
            $ticket->setCustomer($this);
        }
    }

    /**
     * @param Ticket $ticket
     *
     * @return $this
     */
    public function removeTicket(Ticket $ticket)
    {
        $this->tickets->removeElement($ticket);
        $ticket->setCustomer(null);
    }

    /**
     * @param Ticket $ticket
     *
     * @return bool
     */
    public function hasTicket(Ticket $ticket)
    {
        return $this->tickets->contains($ticket);
    }

    /**
     * @return ArrayCollection|Ticket[]
     */
    public function getTickets()
    {
        return $this->tickets;
    }

    /**
     * @param Wishlist $wishlist
     */
    public function addWishlist(Wishlist $wishlist)
    {
        if (!$this->hasWishlist($wishlist)) {
            $this->wishlists->add($wishlist);
            $wishlist->setCustomer($this);
        }
    }

    /**
     * @param Wishlist $wishlist
     */
    public function removeWishlist(Wishlist $wishlist)
    {
        if ($this->hasWishlist($wishlist)) {
            $this->wishlists->removeElement($wishlist);
            $wishlist->setCustomer(null);
        }
    }

    /**
     * @param Wishlist $wishlist
     *
     * @return bool
     */
    public function hasWishlist(Wishlist $wishlist)
    {
        return $this->wishlists->contains($wishlist);
    }

    /**
     * @return ArrayCollection|Wishlist[]
     */
    public function getWishlists()
    {
        return $this->wishlists;
    }

    /**
     * @return string
     */
    public function getInitials()
    {
        return $this->getFirstName()[0] . $this->getLastName()[0];
    }

    /**
     * @return int
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * @param int $number
     *
     * @return $this
     */
    public function setNumber($number)
    {
        $this->number = $number;
    }

    /**
     * @return boolean
     */
    public function getEmailOptIn()
    {
        return $this->emailOptIn;
    }

    /**
     * @param boolean $emailOptIn
     */
    public function setEmailOptIn($emailOptIn)
    {
        $this->emailOptIn = (bool) $emailOptIn;
    }

    /**
     * Get the number type
     *
     * @return string
     */
    public function getSequenceType()
    {
        return self::CUSTOMER_SEQUENCE_TYPE;
    }

    /**
     * @return Channel
     */
    public function getChannel()
    {
        return $this->channel;
    }

    /**
     * @param Channel $channel
     *
     * @return $this
     */
    public function setChannel($channel)
    {
        $this->channel = $channel;
    }

    /**
     * @return string
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * @param $source
     */
    public function setSource($source)
    {
        $this->source = $source;
    }

    /**
     * @return string
     */
    public function getSourceDetail()
    {
        return $this->sourceDetail;
    }

    /**
     * @param $sourceDetail
     */
    public function setSourceDetail($sourceDetail)
    {
        $this->sourceDetail = $sourceDetail;
    }

    /**
     * @param string $countryCode
     */
    public function setCountryCode($countryCode)
    {
        $this->countryCode = $countryCode;
    }

    /**
     * @return string
     */
    public function getCountryCode()
    {
        return $this->countryCode;
    }

    /**
     * @return Store
     */
    public function getStore()
    {
        return $this->store;
    }

    /**
     * @param Store $store
     */
    public function setStore($store)
    {
        $this->store = $store;
    }

    /**
     * @return \DateTime
     */
    public function getFirstSubscribed()
    {
        return $this->firstSubscribed;
    }

    /**
     * @param \DateTime $firstSubscribed
     */
    public function setFirstSubscribed($firstSubscribed)
    {
        $this->firstSubscribed = $firstSubscribed;
    }

    /**
     * @return Collection|CustomerNewsletterSubscription[]
     */
    public function getSubscriptions()
    {
        return $this->subscriptions;
    }

    /**
     * @param ArrayCollection $subscriptions
     */
    public function setSubscriptions($subscriptions)
    {
        $this->subscriptions = $subscriptions;
    }

    /**
     * @param CustomerNewsletterSubscription $subscription
     */
    public function addSubscription(CustomerNewsletterSubscription $subscription)
    {
        foreach ($this->getSubscriptions() as $customerSubscription) {
            if ($customerSubscription->getNewsletter() === $subscription) {
                return;
            }
        }

        $this->subscriptions->add($subscription);
    }

    /**
     * @param CustomerNewsletterSubscription $customerNewsletterSubscription
     */
    public function removeSubscription(CustomerNewsletterSubscription $customerNewsletterSubscription)
    {
        $this->subscriptions->removeElement($customerNewsletterSubscription);
    }

    /**
     * @return Collection|CustomerNewsletterSubscriptionLog[]
     */
    public function getSubscriptionLogs()
    {
        return $this->subscriptionLogs;
    }

    /**
     * @param ArrayCollection $subscriptionLogs
     */
    public function setSubscriptionLogs($subscriptionLogs)
    {
        $this->subscriptionLogs = $subscriptionLogs;
    }
}
