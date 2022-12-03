<h1 align="center">polygontech/nagad-disbursement</h1>

<p align="center">
    <strong>Send money (disburse) to Nagad accounts</strong>
</p>

polygontech/nagad-disbursement is mainly used in laravel projects at polygontech. But it can be worked with any php installation, which will exclude some of its functionalities.

## Installation

The preferred method of installation is via [Composer](https://getcomposer.org/). Run the following
command to install the package and add it as a requirement to your project's
`composer.json`:

```bash
composer require polygontech/nagad-disbursement
```

then, publish the needed config:

```bash
php artisan vendor:publish --provider='Polygontech\NagadDisbursement\ServiceProvider'

# or,

php artisan vendor:publish # and select 'Polygontech\NagadDisbursement\ServiceProvider' when prompted
```

## Usage

Currently, only batch disbursement is supported. For that, first create the `DisbursementBatch`, then call `disburseNow` method on `NagadDisbursement` facade.

```php
use Polygontech\NagadDisbursement\DTO\Input\BatchItem;
use Polygontech\CommonHelpers\Mobile\BDMobile;
use Polygontech\CommonHelpers\Money\BDT;
use Polygontech\NagadDisbursement\DTO\Input\DisbursementBatch;
use Polygontech\NagadDisbursement\Facade\NagadDisbursement;
use Polygontech\NagadDisbursement\DTO\Output\BatchDisburseOutput;
use Carbon\Carbon;

$item1 = new BatchItem(
    account: new BDMobile("+8801687961590"),
    amount: new BDT(1300), // BDT should be created in poysa
    description: "Sample Test Loan",
    additional: [
        "referenceNo" => "10133",
        "someId" => "String",
    ],
);

$item2 = new BatchItem(
    account: new BDMobile("+8801672352566"),
    amount: new BDT(2055), // BDT should be created in poysa
    description: "Sample Test Loan",
    additional: [
        "referenceNo" => "1002",
        "someId" => "String1",
    ],
);

$batch = new DisbursementBatch(
    title: "Batch123456712",
    type: "G2C",
    scheduleTime: Carbon::now(),
    items: [$item1, $item2],
);

/** @var BatchDisburseOutput $output */
$output = NagadDisbursement::disburseNow($batch);

```

## Contributing

Contributions are welcome! To contribute, please familiarize yourself with
[CONTRIBUTING.md](CONTRIBUTING.md).

## Copyright and License

The polygontech/nagad-disbursement library is copyright © [Shafiqul Islam](https://github.com/ShafiqIslam/), [Polygon Technology](https://polygontech.xyz/) and
licensed for use under the MIT License (MIT). Please see [LICENSE](LICENSE) for more
information.
