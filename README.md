# php-jobsystem

[![Build Status](https://travis-ci.org/chesszebra/php-jobsystem.svg?branch=master)](https://travis-ci.org/chesszebra/php-jobsystem)

A PHP library that provides support for executing worker jobs.

## Installation

```bash
composer require chesszebra/jobsystem
```

## Usage

Adding jobs:


```php
<?php

// For this example we use Pheanstalk
$connection = new Pheanstalk\Pheanstalk('localhost');

// The storage to store jobs in
$storage = new ChessZebra\JobSystem\Storage\Pheanstalk($connection);
$storage->addJob(new \ChessZebra\JobSystem\Job\Job('my-worker', [
    'some' => 'parameter',
]));
```

Executing jobs:

```php
<?php

// For this example we use Pheanstalk
$connection = new Pheanstalk\Pheanstalk('localhost');

// The storage where we retrieve jobs from.
$storage = new ChessZebra\JobSystem\Storage\Pheanstalk($connection);

// Setup a logger which is used to write information
$logger = ...; // Any PSR-3 compatible logger.

// A container with all workers
$workers = ...; // Any PSR-11 compatible container.

// Now create the client and run it. 
$client = new ChessZebra\JobSystem\Client($storage, $logger, $workers);
$client->run();
```

## Development

Run composer and phpunit using Docker:

```bash
docker run --rm -it -v $(pwd):/data chesszebra/composer install 
docker run --rm -it -v $(pwd):/data chesszebra/phpunit 
```
