<?php

namespace App;

use Shopify\Auth\FileSessionStorage;
use Shopify\Clients\Rest;
use Shopify\Context;
use Shopify\Auth\OAuth;
use Shopify\Utils;

class ShopifyIntegration {
    public function __construct() {
        Context::initialize(
            $_ENV['SHOPIFY_API_KEY'],
            $_ENV['SHOPIFY_API_SECRET'],
            $_ENV['SHOPIFY_SCOPES'],
            $_ENV['SHOPIFY_HOST'],
            new FileSessionStorage('/tmp/php_sessions'),
            $_ENV['SHOPIFY_API_VERSION'],
            true,
            false
        );
    }

    public function getAuthUrl($shop, $redirectUri) {
        return OAuth::begin(
            $shop,
            $redirectUri,
            true
        );
    }

    public function handleCallback($request) {
        $session = OAuth::callback(
            $request->getQueryParams(),
            $request->getCookieParams()
        );

        return $session;
    }

    public function getClient($session) {
        return new Rest($session->getShop(), $session->getAccessToken());
    }

    public function getProducts($session) {
        $client = $this->getClient($session);
        $response = $client->get('products');
        return $response->getDecodedBody();
    }

    // Add more methods for other Shopify API interactions as needed
}