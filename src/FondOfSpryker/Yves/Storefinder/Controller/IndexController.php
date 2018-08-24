<?php

namespace FondOfSpryker\Yves\Storefinder\Controller;

use Generated\Shared\DataBuilder\StorefinderCustomerAddressBuilder;
use Generated\Shared\Transfer\StorefinderCustomerAddressTransfer;
use Generated\Shared\Transfer\StorefinderSearchRequestTransfer;
use Generated\Shared\Transfer\StorefinderSearchResponseTransfer;
use Spryker\Shared\Config\Config;
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
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function indexAction(Request $request)
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
    public function searchAction(Request $request)
    {
        $storefinderSearchRequest = $this->createStorefinderSearchRequestBy($request);

        $storefinderResponseTransfer = $this->getClient()->search($storefinderSearchRequest);

        $data = [];
        foreach ($storefinderResponseTransfer->getResult() as $storefinderCustomerAddressTransfer) {
            array_push($data, $this->createMarkerResultRowFor($storefinderCustomerAddressTransfer));
        }

        return new JsonResponse($data);
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return string[]|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function detailAction(Request $request)
    {
        $urlKey = $this->getUrlKeyFragment($request->getPathInfo());
        $customerAddressTransfer = $this->getClient()->findOneByUrlKey($urlKey);

        if ($customerAddressTransfer === null) {
            return new RedirectResponse(UrlGenerator::ERROR_PATH);
        }

        $placeholders = [
            'customerAddress' => $customerAddressTransfer,
            'isFromExternalSource' => $this->isFromExternalSource($request),
            'backUrl' => $this->createBackUrl($request),
            'countries' => $this->getSortedAvailableCountries(),
        ];

        return $this->renderView('@Storefinder/detail.twig', $placeholders);
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return string
     */
    protected function createBackUrl(Request $request): string
    {
        $backUrl = '/' . mb_substr($this->getLocale(), 0, 2) . '/storefinder';
        if ($this->isFromExternalSource($request)) {
            return $backUrl;
        }

        return $backUrl . $this->getSearchQuery($request);
    }

    /**
     * @param \Generated\Shared\Transfer\StorefinderCustomerAddressTransfer $storefinderCustomerAddressTransfer
     *
     * @return string[]
     */
    protected function createMarkerResultRowFor(StorefinderCustomerAddressTransfer $storefinderCustomerAddressTransfer): array
    {
        $row = [];
        $row['longitude'] = $storefinderCustomerAddressTransfer->getLongitude();
        $row['latitude'] = $storefinderCustomerAddressTransfer->getLatitude();
        $row['content'] = $this->createDetailPopoverContentFor($storefinderCustomerAddressTransfer);

        return $row;
    }

    /**
     * @param \Generated\Shared\Transfer\StorefinderCustomerAddressTransfer $storefinderCustomerAddressTransfer
     *
     * @return string
     */
    protected function createDetailPopoverContentFor(StorefinderCustomerAddressTransfer $storefinderCustomerAddressTransfer): string
    {
        $placeholders = [
            'name' => $storefinderCustomerAddressTransfer->getName(),
            'street' => $storefinderCustomerAddressTransfer->getStreet(),
            'zipCode' => $storefinderCustomerAddressTransfer->getZipCode(),
            'city' => $storefinderCustomerAddressTransfer->getCity(),
            'urlKey' => $storefinderCustomerAddressTransfer->getUrlKey(),
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
        if (! is_string($referer) || $referer == '') {
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
        if (is_string($zipCode) && $zipCode != '') {
            $query[] = 'address=' . $zipCode;
        }

        $countryCode = $request->get('country');
        if (is_string($countryCode) && $countryCode != '') {
            $query[] = 'country=' . $countryCode;
        }

        if (count($query) == 0) {
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
        if ($totalCustomerAddresses == 0) {
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
        $countries = [
            'DE' => 'Germany',
            'DK' => 'Denmark',
            'FR' => 'France',
            'CH' => 'Switzerland',
            'AT' => 'Austria',
            'BE' => 'Belgium',
            'KR' => 'South Korea',
            'US' => 'United States',
            'CZ' => 'Czech Republic',
            'SK' => 'Slovakia',
            'AE' => 'United Arab Emirates',
            'SA' => 'Saudi Arabia',
            'ES' => 'Spain',
            'LU' => 'Luxembourg',
            'QA' => 'Qatar',
            'TR' => 'Turkey',
            'FI' => 'Finland',
            'MQ' => 'Martinique',
            'GL' => 'Greenland',
            'GP' => 'Guadeloupe',
            'HR' => 'Croatia',
            'IT' => 'Italy',
            'LV' => 'Latvia',
            'PL' => 'Poland',
            'RO' => 'Romania',
            'RU' => 'Russia',
            'SI' => 'Slovenia',
        ];

        asort($countries);

        return $countries;
    }
}
