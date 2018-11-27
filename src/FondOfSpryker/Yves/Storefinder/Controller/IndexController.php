<?php

namespace FondOfSpryker\Yves\Storefinder\Controller;

use Collator;
use Generated\Shared\Transfer\StorefinderCustomerAddressTransfer;
use Generated\Shared\Transfer\StorefinderSearchRequestTransfer;
use Spryker\Yves\Kernel\Controller\AbstractController;
use SprykerShop\Yves\ShopRouter\Generator\UrlGenerator;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @method \FondOfSpryker\Yves\Storefinder\StorefinderFactory getFactory()
 * @method \FondOfSpryker\Client\Storefinder\StorefinderClientInterface getClient()
 */
class IndexController extends AbstractController
{
    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request): Response
    {
        $storefinderSearchRequest = $this->createStorefinderSearchRequestBy($request);
        $storefinderSearchRequest->setLimit($this->getNumberOfResultsPerPage());
        $storefinderSearchRequest->setLimitStart($this->getLimitStart($request->get('p', 1)));

        $storefinderResponseTransfer = $this->getClient()->search($storefinderSearchRequest);

        $placeholders = [
            'countries' => $this->getSortedAvailableCountries(),
            'customerAddresses' => $storefinderResponseTransfer->getResult(),
            'numberOfPages' => $this->getNumberOfPages($storefinderResponseTransfer->getResultCount()),
            'searchQuery' => $this->getSearchQuery($request),
            'currentPageNumber' => $request->get('p', 1),
            'selectedCountry' => $request->get('country', ''),
            'selectedZipCode' => $request->get('address', ''),
        ];

        return $this->renderView('@Storefinder/index.twig', $placeholders);
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return array|\Symfony\Component\HttpFoundation\JsonResponse
     */
    public function searchAction(Request $request): Response
    {
        $storefinderSearchRequest = $this->createStorefinderSearchRequestBy($request);

        $storefinderResponseTransfer = $this->getClient()->search($storefinderSearchRequest);

        $data = [];
        foreach ($storefinderResponseTransfer->getResult() as $storefinderCustomerAddressTransfer) {
            $data[] = $this->createMarkerResultRowFor($storefinderCustomerAddressTransfer, $request);
        }

        return new JsonResponse($data);
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function detailAction(Request $request): Response
    {
        $urlKey = $this->getUrlKeyFragment($request->getPathInfo());
        $customerAddressTransfer = $this->getClient()->findOneByUrlKey($urlKey);

        if ($customerAddressTransfer === null) {
            return new RedirectResponse(UrlGenerator::ERROR_PATH);
        }

        $placeholders = [
            'customerAddress' => $customerAddressTransfer,
            'isFromExternalSource' => $this->isFromExternalSource($request),
            'queryString' => $this->createQueryString($request),
            'countries' => $this->getSortedAvailableCountries(),
        ];

        return $this->renderView('@Storefinder/detail.twig', $placeholders);
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return string
     */
    protected function createQueryString(Request $request): string
    {
        if (!$this->isFromExternalSource($request) && $request->getQueryString() !== '') {
            return '?' . $request->getQueryString();
        }

        return '';
    }

    /**
     * @param \Generated\Shared\Transfer\StorefinderCustomerAddressTransfer $storefinderCustomerAddressTransfer
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return string[]
     */
    protected function createMarkerResultRowFor(StorefinderCustomerAddressTransfer $storefinderCustomerAddressTransfer, Request $request): array
    {
        $row = [];
        $row['longitude'] = $storefinderCustomerAddressTransfer->getLongitude();
        $row['latitude'] = $storefinderCustomerAddressTransfer->getLatitude();
        $row['content'] = $this->createDetailPopoverContentFor($storefinderCustomerAddressTransfer, $request);

        return $row;
    }

    /**
     * @param \Generated\Shared\Transfer\StorefinderCustomerAddressTransfer $storefinderCustomerAddressTransfer
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return string
     */
    protected function createDetailPopoverContentFor(StorefinderCustomerAddressTransfer $storefinderCustomerAddressTransfer, Request $request): string
    {
        $placeholders = [
            'name' => $storefinderCustomerAddressTransfer->getName(),
            'street' => $storefinderCustomerAddressTransfer->getStreet(),
            'zipCode' => $storefinderCustomerAddressTransfer->getZipCode(),
            'city' => $storefinderCustomerAddressTransfer->getCity(),
            'urlKey' => $storefinderCustomerAddressTransfer->getUrlKey(),
            'currentLanguage' => $request->get('lang'),
            'searchQuery' => $this->getSearchQuery($request),
        ];

        return $this->renderView('@Storefinder/map-popover.twig', $placeholders)->getContent();
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return bool
     */
    protected function isFromExternalSource(Request $request): bool
    {
        $referer = $request->server->get('HTTP_REFERER');
        if (!\is_string($referer) || $referer === '') {
            return true;
        }

        return strpos($referer, 'storefinder') === false;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Generated\Shared\Transfer\StorefinderSearchRequestTransfer
     */
    protected function createStorefinderSearchRequestBy(Request $request): StorefinderSearchRequestTransfer
    {
        $storefinderSearchRequest = new StorefinderSearchRequestTransfer();
        $storefinderSearchRequest->setCountryCode($request->get('country'));
        $storefinderSearchRequest->setAddress($request->get('address'));

        return $storefinderSearchRequest;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return string
     */
    protected function getSearchQuery(Request $request): string
    {
        $query = [];

        $zipCode = $request->get('address');
        if (\is_string($zipCode) && $zipCode !== '') {
            $query[] = 'address=' . $zipCode;
        }

        $countryCode = $request->get('country');
        if (\is_string($countryCode) && $countryCode !== '') {
            $query[] = 'country=' . $countryCode;
        }

        if (count($query) === 0) {
            return '';
        }

        return '?' . implode('&', $query);
    }

    /**
     * @param int $totalCustomerAddresses
     *
     * @return int
     */
    protected function getNumberOfPages(int $totalCustomerAddresses): int
    {
        if ($totalCustomerAddresses === 0) {
            return 1;
        }

        return ceil($totalCustomerAddresses / $this->getNumberOfResultsPerPage());
    }

    /**
     * @return int
     */
    protected function getNumberOfResultsPerPage(): int
    {
        return 24;
    }

    /**
     * @param int $currentPage
     *
     * @return int
     */
    protected function getLimitStart(int $currentPage): int
    {
        if ($currentPage <= 1) {
            return 0;
        }

        return ($currentPage - 1) * $this->getNumberOfResultsPerPage();
    }

    /**
     * @param string $uri
     *
     * @return string
     */
    protected function getUrlKeyFragment(string $uri): string
    {
        $uriFragments = explode('/', $uri);

        return end($uriFragments);
    }

    /**
     * @return string[]
     */
    protected function getSortedAvailableCountries(): array
    {
        $glossaryStorageClient = $this->getFactory()->getGlossaryStorageClient();
        $locale = $this->getLocale();

        $countries = [
            'DE' => $glossaryStorageClient->translate('countries.iso.de', $locale),
            'DK' => $glossaryStorageClient->translate('countries.iso.dk', $locale),
            'FR' => $glossaryStorageClient->translate('countries.iso.fr', $locale),
            'CH' => $glossaryStorageClient->translate('countries.iso.ch', $locale),
            'AT' => $glossaryStorageClient->translate('countries.iso.at', $locale),
            'BE' => $glossaryStorageClient->translate('countries.iso.be', $locale),
            'KR' => $glossaryStorageClient->translate('countries.iso.kr', $locale),
            'US' => $glossaryStorageClient->translate('countries.iso.us', $locale),
            'CZ' => $glossaryStorageClient->translate('countries.iso.cz', $locale),
            'SK' => $glossaryStorageClient->translate('countries.iso.SK', $locale),
            'AE' => $glossaryStorageClient->translate('countries.iso.AE', $locale),
            'SA' => $glossaryStorageClient->translate('countries.iso.SA', $locale),
            'ES' => $glossaryStorageClient->translate('countries.iso.ES', $locale),
            'LU' => $glossaryStorageClient->translate('countries.iso.LU', $locale),
            'QA' => $glossaryStorageClient->translate('countries.iso.QA', $locale),
            'TR' => $glossaryStorageClient->translate('countries.iso.TR', $locale),
            'FI' => $glossaryStorageClient->translate('countries.iso.FI', $locale),
            'MQ' => $glossaryStorageClient->translate('countries.iso.MQ', $locale),
            'GL' => $glossaryStorageClient->translate('countries.iso.GL', $locale),
            'GP' => $glossaryStorageClient->translate('countries.iso.GP', $locale),
            'HR' => $glossaryStorageClient->translate('countries.iso.HR', $locale),
            'IT' => $glossaryStorageClient->translate('countries.iso.IT', $locale),
            'LV' => $glossaryStorageClient->translate('countries.iso.LV', $locale),
            'PL' => $glossaryStorageClient->translate('countries.iso.PL', $locale),
            'RO' => $glossaryStorageClient->translate('countries.iso.RO', $locale),
            'RU' => $glossaryStorageClient->translate('countries.iso.RU', $locale),
            'SI' => $glossaryStorageClient->translate('countries.iso.SI', $locale),
        ];

        $collator = new Collator($locale);
        $collator->asort($countries, Collator::SORT_STRING);

        return $countries;
    }
}
