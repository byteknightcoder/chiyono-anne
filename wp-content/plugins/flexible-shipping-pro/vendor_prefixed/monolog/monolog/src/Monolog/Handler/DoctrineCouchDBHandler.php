<?php

declare (strict_types=1);
/*
 * This file is part of the Monolog package.
 *
 * (c) Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace FSProVendor\Monolog\Handler;

use FSProVendor\Monolog\Logger;
use FSProVendor\Monolog\Formatter\NormalizerFormatter;
use FSProVendor\Monolog\Formatter\FormatterInterface;
use FSProVendor\Doctrine\CouchDB\CouchDBClient;
/**
 * CouchDB handler for Doctrine CouchDB ODM
 *
 * @author Markus Bachmann <markus.bachmann@bachi.biz>
 */
class DoctrineCouchDBHandler extends \FSProVendor\Monolog\Handler\AbstractProcessingHandler
{
    /** @var CouchDBClient */
    private $client;
    public function __construct(\FSProVendor\Doctrine\CouchDB\CouchDBClient $client, $level = \FSProVendor\Monolog\Logger::DEBUG, bool $bubble = \true)
    {
        $this->client = $client;
        parent::__construct($level, $bubble);
    }
    /**
     * {@inheritDoc}
     */
    protected function write(array $record) : void
    {
        $this->client->postDocument($record['formatted']);
    }
    protected function getDefaultFormatter() : \FSProVendor\Monolog\Formatter\FormatterInterface
    {
        return new \FSProVendor\Monolog\Formatter\NormalizerFormatter();
    }
}
