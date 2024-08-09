<?php

namespace GhoSter\KbankPayments\Model;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use GhoSter\KbankPayments\Model\ResourceModel\Meta\CollectionFactory as MetaCollectionFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Reflection\DataObjectProcessor;
use GhoSter\KbankPayments\Api\Data\MetaInterfaceFactory;
use GhoSter\KbankPayments\Api\MetaRepositoryInterface;
use GhoSter\KbankPayments\Api\Data\MetaSearchResultsInterfaceFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Api\SortOrder;
use GhoSter\KbankPayments\Model\ResourceModel\Meta as ResourceMeta;
use Magento\Framework\Api\DataObjectHelper;

class MetaRepository implements MetaRepositoryInterface
{
    /**
     * @var DataObjectHelper
     */
    protected $dataObjectHelper;

    /**
     * @var ResourceMeta
     */
    protected $resource;

    /**
     * @var MetaInterfaceFactory
     */
    protected $dataMetaFactory;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var MetaSearchResultsInterfaceFactory
     */
    protected $searchResultsFactory;

    /**
     * @var MetaCollectionFactory
     */
    protected $metaCollectionFactory;

    /**
     * @var DataObjectProcessor
     */
    protected $dataObjectProcessor;

    /**
     * @var MetaFactory
     */
    protected $metaFactory;

    /**
     * @param ResourceMeta $resource
     * @param MetaFactory $metaFactory
     * @param MetaInterfaceFactory $dataMetaFactory
     * @param MetaCollectionFactory $metaCollectionFactory
     * @param MetaSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        ResourceMeta $resource,
        MetaFactory $metaFactory,
        MetaInterfaceFactory $dataMetaFactory,
        MetaCollectionFactory $metaCollectionFactory,
        MetaSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager
    ) {
        $this->resource = $resource;
        $this->metaFactory = $metaFactory;
        $this->metaCollectionFactory = $metaCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataMetaFactory = $dataMetaFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
    }

    /**
     * @inheritdoc
     */
    public function save(
        \GhoSter\KbankPayments\Api\Data\MetaInterface $meta
    ) {
        try {
            $this->resource->save($meta);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the meta: %1',
                $exception->getMessage()
            ));
        }
        return $meta;
    }

    /**
     * @inheritdoc
     */
    public function getById($metaId)
    {
        $meta = $this->metaFactory->create();
        $this->resource->load($meta, $metaId);
        if (!$meta->getId()) {
            throw new NoSuchEntityException(__('Meta with id "%1" does not exist.', $metaId));
        }
        return $meta;
    }

    /**
     * @inheritdoc
     */
    public function getByOrderIncrement($orderIncrementId)
    {
        $meta = $this->metaFactory->create();
        $this->resource->loadByOrderIncrement($meta, $orderIncrementId);
        return $meta;
    }

    /**
     * @inheritdoc
     */
    public function getByChargeId($chargeId)
    {
        $meta = $this->metaFactory->create();
        $this->resource->loadByChargeId($meta, $chargeId);
        if (!$meta->getId()) {
            throw new NoSuchEntityException(__('Meta with charge id %1 does not exist.', $chargeId));
        }
        return $meta;
    }

    /**
     * @inheritdoc
     */
    public function getList(
        SearchCriteriaInterface $searchCriteria
    ) {
        $collection = $this->metaCollectionFactory->create();
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
        \GhoSter\KbankPayments\Api\Data\MetaInterface $meta
    ) {
        try {
            $this->resource->delete($meta);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the Meta: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * @inheritdoc
     */
    public function deleteById($metaId)
    {
        return $this->delete($this->getById($metaId));
    }
}
