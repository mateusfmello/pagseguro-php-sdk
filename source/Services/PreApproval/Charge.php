<?php
/**
 * 2007-2016 [PagSeguro Internet Ltda.]
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 * @author    PagSeguro Internet Ltda.
 * @copyright 2007-2016 PagSeguro Internet Ltda.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 *
 */

namespace PagSeguro\Services\PreApproval;

use PagSeguro\Domains\Account\Credentials;
use PagSeguro\Helpers\Crypto;
use PagSeguro\Resources\Connection;
use PagSeguro\Resources\Http;
use PagSeguro\Resources\Log\Logger;
use PagSeguro\Parsers\PreApproval\Charge\Request;
use PagSeguro\Resources\Responsibility;

class Charge
{

    public static function create(Credentials $credentials, \PagSeguro\Domains\Requests\PreApproval\Charge $charge)
    {
        Logger::info("Begin", ['service' => 'PreApproval.Charge']);
        try {
            $connection = new Connection\Data($credentials);
            $http = new Http();
            Logger::info(sprintf("POST: %s", self::request($connection)), ['service' => 'PreApproval.Charge']);
            Logger::info(sprintf(
                "Params: %s",
                json_encode(Crypto::encrypt(Request::getData($charge)))),
                ['service' => 'PreApproval.Charge']
            );
            $http->post(
                self::request($connection),
                Request::getData($charge)
            );

            return Responsibility::http(
                $http,
                new Request
            );
        } catch (\Exception $exception) {
            Logger::error($exception->getMessage(), ['service' => 'PreApproval.Charge']);
            throw $exception;
        }
    }

    /**
     * @param Connection\Data $connection
     * @return string
     */
    private static function request(Connection\Data $connection)
    {
        return $connection->buildPreApprovalChargeRequestUrl() ."?". $connection->buildCredentialsQuery();
    }
}
