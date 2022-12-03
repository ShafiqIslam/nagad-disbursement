<?php

return [

    /**
     * Disbursement related configurations.
     */
    "disbursement" => [

        /**
         * Base url of nagad disbursement service
         */
        "base_url" => env("NAGAD_DISBURSE_BASE_URL", "http://sandbox.mynagad.com:30001"),

        /**
         * This is a unique identifier provided to every merchant aggregator.
         * The Merchant Aggregator ID is part of the merchant aggregator’s account credentials.
         */
        "merchant_aggegator_id" => env("NAGAD_DISBURSE_MA_ID", "660716583969324"),

        /**
         * This is a unique identifier provided to every merchant.
         * The Merchant ID is part ofthe merchant’s account credentials.
         */
        "merchant_id" => env("NAGAD_DISBURSE_MC_ID", "683001003835399"),

        /**
         * Nagad will provide RSA public key which will be available in portal after merchant and merchant aggregator registration.
         * Merchant aggregator will verify signature with this public key which will be sent from Nagad External gateway Initialization API.
         */
        "pg_public_key" => env("NAGAD_DISBURSE_PG_PUBLIC_KEY", "MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAjv/KdCoowaDftlrgiqEUbOjA5SPxLCB9MO4fBikmlTnNr0UPqDmvSWS3q3GmGPkZ19bnyJfsn3s1lEtnULqgcat4rNO2XwEjtum0VncQ8rLomYnzzdmY3NXnWmFpiaD7LRcPvQL3Vh90YpfPMD3JXcOCjmJ2uAuE0PnSUqipDqSP5Hzcw7yLHCY2BqylG1KXHYgmfIoAexZoKRVFz+fhhkOQFqjSySZwN+WOiXSwjtY45uhzt2Rqny/t3+rgI8J9ZNMJfMokZFUQWtkmM4G/Z9Voj2iSkxOQLxyTgVfXhhZ1yBZQvboG6huEcEA0tlpTokis65wBZqrQ7P+mBQTq8QIDAQAB"),

        /**
         * Merchant Aggregator will generate RSA Key Pair.
         * Merchant aggregator will upload public key to Nagad server via Portal.
         * Nagad server will encrypt Initialization API response sensitive data using the public key.
         * Merchant aggregator will use the private key to decrypt Initialization API response sensitive data.
         */
        "merchant_private_key" => env("NAGAD_DISBURSE_MERCHANT_PRIVATE_KEY", "MIIEvgIBADANBgkqhkiG9w0BAQEFAASCBKgwggSkAgEAAoIBAQDSvHN4y0/3CKefikqSOa7NDsnGgtDlU7P6OvHMnskha1HNCT5pBK97skFoiXJ4p7ZYbjRqH/coL3ceww0arXcA+60SBG4IjkWvogpaIJCSUV5dh0fjv7WUpAQDLZjtk+yLVsqp98HUu25pTg9DdEGSgM/1E3/l8jh5RKU+GKm9/VmXjN9talnnldCNPIczhEGI+XzOs/edB3PjJqcCGDZbEU+85FmisSPJRQZu1hyOZ3j2rgnZPI5hIxl7oi3iSnzqy9jHvKhXl8bNiNlh/14g0BI7OpNEXKgnFRuIGLjFk/1W7lfDwTE7rjjP5feJNGYkkNBy4tS3cG8frZmwH8eFAgMBAAECggEBAMVegrerk6VGkde2ackyBSlApHIrqwJdtr6x3i1Kug12uhJSigVJwiET/nat5Gxkhz+jV6vdbFpSujoxbGCD/mUJUjsBsxyIQ3QPS6rFvSGM29i1DvubXbFtO+TOG+DHHlASZZVy2jMnqG0wEtOWWqOfySU7shnnFkVdqXXTG4c3we40w/mRv4vm1vlD+OTt6ULlpUj0ybcZhZHuCsDP3JVfX4EeDByzZ0OMzJPe+pIw9pdTbyjm1SnOw3mm46Mu3T2ePoR+/OK4t4bYM62wPS+YQp78KCOpU4AZs/06FmjXQnWFTCxBWsry5qr8bS+NNDXa5RHqivhJRf2505sUhIECgYEA8klVhFEy88dKaqGDJug+9tzkBe4YUschs/n8D3OAPE8u/qco6HcjzwxoYctvR1F5QBh0bY3gFT+hLiP/ZvqOeaVNGKXEUZM8AyVKUUJQt7Ge9D+GUxzVvdOUyqNEBHYoOObifW1njLgq1DT24wHUt7keObMW+o9XugI2pB91NxkCgYEA3qn2FUP/gaEkzGddrXczrpmJddDEZ+jrUafJmC3WmUe1lgiUXY5iSgPUt9qw3JtkM2UU5cxP60GfXAAoQH+n26W9wvDQ2Dg7SjUEJcUT0f6Gm6i6aQdF0mneOVeNCS5QpM20mAynuQyXDjJAitgtEPQEnolpVeprKW7Pwc+PfU0CgYA6kDFEf2ACfrxlE88gu2hkwTW4nTlx4MIrv5QGpBNuAHHKidsgfZPBOy2L3eFy6qWVMZQK2w079ZpfDcJxQMTpcGQ3PfI7CYyq8fuJsq7SB/P089njAwhDDv5bEKWjMnA8eMpsKOKrp+RqULcQXePt2KgOqFQ4kidRRbGxcA0kGQKBgBY1q8cZPj4m3a3Jza1Ey5Hp3K00wrJ+qCI/8zrLr7EgVvt9JZdjYWhyk2A3XxbSJR9/QKNfSsVziTq7BUjRsuOU16W/MYWvrjJLsXs2+jAjDDgwj090m0FOsAzWL8ovpXmazx2vfXdWyyZuWsO+plgfjuplWG1qcX/zfqOdWXmtAoGBAJp5Dcqw+5YizboOIgyCfXmy/TRcbl/ZkAjs5bAShyQxC3SxJ74vDOkGGOyXSW7WFDDiK3JaxONfYTA30dZDkgOfQnQd0yA+/avaCfhPBJzgVJCQAlV/09Md8w7Z0LXJfeU7FLimu2jxWCcC7bl7l4QSMC6g2bF6WTifYQRoszY4"),

        /**
         * Merchant will have a HMAC Key.
         * The key will be used to generate signature on merchant provided recharge request transaction data before sending to Merchant Aggregator.
         * Nagad server will verify transactional data signature using the same key.
         * Merchant will upload the key to Nagad server via Portal.
         */
        "hmac_key" => env("NAGAD_DISBURSE_HMAC_KEY", "532CB0248BA95B0A7A1050BA14D6E78065E60E11DF4B99C43141E596F59F4B2D"),
    ]
];
