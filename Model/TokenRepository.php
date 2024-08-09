<?php

namespace GhoSter\KbankPayments\Model;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use GhoSter\KbankPayments\Model\ResourceModel\Token\CollectionFactory as TokenCollectionFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Reflection\DataObjectProcessor;
use GhoSter\KbankPayments\Api\Data\TokenInterfaceFactory;
use GhoSter\KbankPayments\Api\TokenRepositoryInterface;
use GhoSter\KbankPayments\Api\Data\TokenSearchResultsInterfaceFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Api\SortOrder;
use GhoSter\KbankPayments\Model\ResourceModel\Token as ResourceToken;
use Magento\Framework\Api\DataObjectHelper;

class TokenRepository implements TokenRepositoryInterface
{
    /**
     * @var DataObjectHelper
     */
    protected $dataObjectHelper;

    /**
     * @var ResourceToken
     */
    protected $resource;

    /**
     * @var TokenInterfaceFactory
     */
    protected $dataTokenFactory;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var TokenSearchResultsInterfaceFactory
     */
    protected $searchResultsFactory;

    /**
     * @var TokenCollectionFactory
     */
    protected $tokenCollectionFactory;

    /**
     * @var DataObjectProcessor
     */
    protected $dataObjectProcessor;

    /**
     * @var TokenFactory
     */
    protected $tokenFactory;

    /**
     * @param ResourceToken $resource
     * @param TokenFactory $tokenFactory
     * @param TokenInterfaceFactory $dataTokenFactory
     * @param TokenCollectionFactory $tokenCollectionFactory
     * @param TokenSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        ResourceToken $resource,
        TokenFactory $tokenFactory,
        TokenInterfaceFactory $dataTokenFactory,
        TokenCollectionFactory $tokenCollectionFactory,
        TokenSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager
    ) {
        $this->resource = $resource;
        $this->tokenFactory = $tokenFactory;
        $this->tokenCollectionFactory = $tokenCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataTokenFactory = $dataTokenFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
    }

    /**
     * @inheritdoc
     */
    public function save(
        \GhoSter\KbankPayments\Api\Data\TokenInterface $token
    ) {
        try {
            $this->resource->save($token);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the token: %1',
                $exception->getMessage()
            ));
        }
        return $token;
    }

    /**
     * @inheritdoc
     */
    public function getById($tokenId)
    {
        $token = $this->tokenFactory->create();
        $this->resource->load($token, $tokenId);
        if (!$token->getId()) {
            throw new NoSuchEntityException(__('Token with id "%1" does not exist.', $tokenId));
        }
        return $token;
    }

    /**
     * @inheritdoc
     */
    public function getByOrderIncrement($orderIncrementId)
    {
        $token = $this->tokenFactory->create();
        $this->resource->loadByOrderIncrement($token, $orderIncrementId);
        if (!$token->getId()) {
            throw new NoSuchEntityException(
                __('Token with order increment id "%1" does not exist.', $orderIncrementId)
            );
        }
        return $token;
    }

    /**
     * @inheritdoc
     */
    public function getByToken($token)
    {
        $tokenModel = $this->tokenFactory->create();
        $this->resource->loadByToken($tokenModel, $token);
        if (!$tokenModel->getId()) {
            throw new NoSuchEntityException(
                __('Token with token value id "%1" does not exist.', $token)
            );
        }
        return $tokenModel;
    }

    /**
     * @inheritdoc
     */
    public function getList(
        SearchCriteriaInterface $searchCriteria
    ) {
        $collection = $this->tokenCollectionFactory->create();
        foreach ($searchCriteria->getFilterGroups() as $filterGroup) {
            $fields = [];
            $conditions = [];
            foreach ($filterGroup->getFilters() as $filter) {
                if ($filter->getField() === 'store_id') {
                    $collection->addStoreFilter($filter->getValue(), false);
                    continue;
                }
                $fields[] = $filter->getField();
                $condition = $filter->getConditionType() ?: 'eq';
                $conditions[] = [$condition => $filter->getValue()];
            }
            $collection->addFieldToFilter($fields, $conditions);
        }

        $sortOrders = $searchCriteria->getSortOrders();
        if ($sortOrders) {
            /** @var SortOrder $sortOrder */
            foreach ($sortOrders as $sortOrder) {
                $collection->addOrder(
                    $sortOrder->getField(),
                    ($sortOrder->getDirection() == SortOrder::SORT_ASC) ? 'ASC' : 'DESC'
                );
            }
        }
        $collection->setCurPage($searchCriteria->getCurrentPage());
        $collection->setPageSize($searchCriteria->getPageSize());

        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setTotalCount($collection->getSize());
        $searchResults->setItems($collection->getItems());
        return $searchResults;
    }

    /**
     * @inheritdoc
     */
    public function delete(
        \GhoSter\KbankPayments\Api\Data\TokenInterface $token
    ) {
        try {
            $this->resource->delete($token);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the Token: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * @inheritdoc
     */
    public function deleteById($tokenId)
    {
        return $this->delete($this->getById($tokenId));
    }
}
