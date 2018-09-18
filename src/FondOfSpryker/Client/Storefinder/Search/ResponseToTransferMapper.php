<?php

namespace FondOfSpryker\Client\Storefinder\Search;

use Generated\Shared\Transfer\StorefinderCustomerAddressTransfer;
use Generated\Shared\Transfer\StorefinderSearchResponseTransfer;

class ResponseToTransferMapper implements ResponseToTransferMapperInterface
{
    /**
     * @return \Generated\Shared\Transfer\StorefinderSearchResponseTransfer
     */
    protected function createStorefinderSearchResponseTransfer(): StorefinderSearchResponseTransfer
    {
        return new StorefinderSearchResponseTransfer();
    }

    /**
     * @return \Generated\Shared\Transfer\StorefinderCustomerAddressTransfer
     */
    protected function createStorefinderCustomerAddressTransfer(): StorefinderCustomerAddressTransfer
    {
        return new StorefinderCustomerAddressTransfer();
    }

    /**
     * @param string[] $elasticsearchResponse
     *
     * @return \Generated\Shared\Transfer\StorefinderSearchResponseTransfer
     */
    public function mapResponseToTransfer(array $elasticsearchResponse): StorefinderSearchResponseTransfer
    {
        $responseTransfer = $this->createStorefinderSearchResponseTransfer();

        if ($this->hasHits($elasticsearchResponse)) {
            foreach ($this->getHits($elasticsearchResponse) as $hit) {
                $responseTransfer->addResult($this->mapCustomAddressTransfer($hit));
            }
        }

        $totalHits = $this->getTotal($elasticsearchResponse);
        $responseTransfer->setResultCount($totalHits);

        return $responseTransfer;
    }

    /**
     * @param string[] $elasticsearchResponse
     *
     * @return bool
     */
    protected function getTotal(array $elasticsearchResponse): int
    {
        return $elasticsearchResponse['hits']['total'];
    }

    /**
     * @param string[] $elasticsearchResponse
     *
     * @return string[]
     */
    protected function getHits(array $elasticsearchResponse): array
    {
        return $elasticsearchResponse['hits']['hits'];
    }

    /**
     * @param string[] $elasticsearchResponse
     *
     * @return bool
     */
    protected function hasHits(array $elasticsearchResponse): bool
    {
        return $this->getTotal($elasticsearchResponse) > 0;
    }

    /**
     * @param string $name
     * @param string[] $hit
     *
     * @return bool
     */
    protected function hasValue(string $name, array $hit): bool
    {
        return array_key_exists($name, $hit['_source']);
    }

    /**
     * @param string $name
     * @param string[] $hit
     *
     * @return string
     */
    protected function getValue(string $name, array $hit): ?string
    {
        if ($this->hasValue($name, $hit)) {
            return $hit['_source'][$name];
        }

        return null;
    }

    /**
     * @param string[] $hit
     *
     * @return \Generated\Shared\Transfer\StorefinderCustomerAddressTransfer
     */
    protected function mapCustomAddressTransfer(array $hit): StorefinderCustomerAddressTransfer
    {
        $customerAddressTransfer = $this->createStorefinderCustomerAddressTransfer();

        $name = $this->getValue('storename', $hit);
        if (!is_string($name) || $name === '') {
            $name = $this->getValue('company', $hit);
        }

        $customerAddressTransfer->setName($name);

        $zipCode = $this->getValue('postcode', $hit);
        $customerAddressTransfer->setZipCode($zipCode);

        $city = $this->getValue('city', $hit);
        $customerAddressTransfer->setCity($city);

        $latitude = $this->getValue('fob_custkpi_lat', $hit);
        $customerAddressTransfer->setLatitude($latitude);

        $longitude = $this->getValue('fob_custkpi_lng', $hit);
        $customerAddressTransfer->setLongitude($longitude);

        $mail = $this->getValue('custaddr_email', $hit);
        $customerAddressTransfer->setMail($mail);

        $entityId = $this->getValue('entity_id', $hit);
        $customerAddressTransfer->setStoreId($entityId);

        $website = $this->getValue('fob_custkpi_website_for_search', $hit);
        $customerAddressTransfer->setWebsite($website);

        $phone = $this->getValue('fob_custkpi_phone_for_search', $hit);
        $customerAddressTransfer->setPhone($phone);

        $street = $this->getValue('street', $hit);
        $customerAddressTransfer->setStreet($street);

        $countryId = $this->getValue('country_id', $hit);
        $customerAddressTransfer->setCountryCode($countryId);

        $urlKey = $this->getValue('custaddr_url_key', $hit);
        $customerAddressTransfer->setUrlKey($urlKey);

        return $customerAddressTransfer;
    }
}
