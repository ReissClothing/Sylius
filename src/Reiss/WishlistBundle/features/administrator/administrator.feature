@wishlist
Feature: Wishlist
    In order to increase the sales
    As a store owner
    I want to see wishlists statistics

    Scenario: Only administrate wishlist if logged in as an administrator
        When  I go to the reiss backend wishlist statistics page
        Then  I should not be able to access the administration page