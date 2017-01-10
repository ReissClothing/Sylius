@wishlist
Feature: Wishlist
    In order to allow visitors to have wishlists
    As a visitor
    I want to create wishlists

    Scenario: I should have as title "Username's Wishlists"
        Given there are customers:
            | email              | password | enabled | firstName |
            | sylius@example.com | pass     | true    | Orrin     |
            | carlos@example.com | pass     | true    | Carlos    |
        Then I should have the title "Orrin's Wishlists" for "Orrin"
        Then I should have the title "Carlos' Wishlists" for "Carlos"

    Scenario: I should have always a primary list
        Given there are customers:
            | email              | password | enabled | firstName |
            | sylius@example.com | pass     | true    | Orrin     |
        Given there are the following wishlists:
            | name            | customer               | primary_wishlist |
            | My collection 1 | sylius@example.com | true             |
            | My collection 2 | sylius@example.com | false            |
            | My collection 3 | sylius@example.com | false            |
        And I remove the wishlist "My collection 1"
        Then The wishlist "My collection 2" should be the primary one

    Scenario: Variants should be always added to the primary wishlist
        Given there are following reiss products:
            | name          | colour | sizes          | sku     | on_hand        | season |
            | Neck dress    | blue   | 26, 28, 31, 32 | 1234567 | 62, 82, 13, 23 | AW10   |
        And there are customers:
            | email              | password | enabled | firstName |
            | sylius@example.com | pass     | true    | Orrin     |
        And there are the following wishlists:
            | name            | customer               | primary_wishlist |
            | My collection 1 | sylius@example.com | 1             |
            | My collection 2 | sylius@example.com | 0            |
            | My collection 3 | sylius@example.com | 0            |
        When I add a "Neck dress" with size "28" for "sylius@example.com"
        Then I should have "Neck dress" in "My collection 1"