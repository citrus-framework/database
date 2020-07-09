<?php

declare(strict_types=1);

/**
 * @copyright   Copyright 2020, CitrusFramework. All Rights Reserved.
 * @author      take64 <take64@citrus.tk>
 * @license     http://www.citrus.tk/
 */

namespace Citrus\Database;

use Citrus\Configure\Configurable;
use Citrus\Database\DSN\Postgres;
use Citrus\Database\DSN\Sqlite;
use Citrus\Variable\Binders;
use Citrus\Variable\Instance;

/**
 * DSN定義
 */
class DSN extends Configurable
{
    use Instance;
    use Binders;
    use Postgres;
    use Sqlite;

    /** @var string */
    public $type;

    /** @var string */
    public $hostname;

    /** @var string */
    public $port;

    /** @var string */
    public $database;

    /** @var string */
    public $schema;

    /** @var string */
    public $username;

    /** @var string */
    public $password;

    /** @var array */
    public $options;



    /**
     * {@inheritDoc}
     *
     * @return self
     */
    public function loadConfigures(array $configures = []): Configurable
    {
        parent::loadConfigures($configures);

        // bind
        $this->bindArray($this->configures);

        return $this;
    }



    /**
     * generate dsn string
     *
     * @return string
     */
    public function toString()
    {
        // PostgreSQL
        if (true === $this->isPostgreSQL())
        {
            return sprintf('pgsql:host=%s;port=%s;dbname=%s',
                $this->hostname,
                $this->port,
                $this->database
            );
        }
        // SQLite
        if (true === $this->isSQLite())
        {
            return sprintf('sqlite:%s',
                $this->hostname
            );
        }

        return '';
    }



    /**
     * generate dsn string with authentication
     *
     * @return string
     */
    public function toStringWithAuth()
    {
        // PostgreSQL
        if (true === $this->isPostgreSQL())
        {
            return sprintf('pgsql:host=%s;port=%s;dbname=%s;user=%s;password=%s',
                $this->hostname,
                $this->port,
                $this->database,
                $this->username,
                $this->password
            );
        }
        // SQLite
        if (true === $this->isSQLite())
        {
            return sprintf('sqlite:%s',
                $this->hostname
            );
        }

        return '';
    }



    /**
     * {@inheritDoc}
     */
    protected function configureKey(): string
    {
        return 'database';
    }



    /**
     * {@inheritDoc}
     */
    protected function configureDefaults(): array
    {
        return [];
    }



    /**
     * {@inheritDoc}
     */
    protected function configureRequires(): array
    {
        return [
            'type',
            'hostname',
        ];
    }
}
