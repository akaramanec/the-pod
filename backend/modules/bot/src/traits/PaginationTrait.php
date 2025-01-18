<?php

namespace backend\modules\bot\src\traits;

use Yii;
use yii\data\Pagination;

trait PaginationTrait
{
    private $_page;
    private $_totalCount;
    private $_totalCountPage;
    private $_perPage = 10;

    private function getProduct()
    {
        $this->session->set('nextPaginationAction', '/TCatalog_next');
        $this->session->set('prevPaginationAction', '/TCatalog_prev');
        $query = $this->query();
        return $this->setPaginationByQuery($query);
    }

    public function prev()
    {
        $this->setPrevPage();
        $this->productsFilter();
    }

    public function prevSearchPage()
    {
        $this->setPrevPage();
        $this->searchProducts();
    }

    public function next()
    {
        $this->setNextPage();
        $this->productsFilter();
    }

    public function nextSearchPage()
    {
        $this->setNextPage();
        $this->searchProducts();
    }

    /**
     * @param \yii\db\ActiveQuery $query
     * @return array|\yii\db\ActiveRecord[]
     */
    private function setPaginationByQuery(\yii\db\ActiveQuery $query): array
    {
        Yii::$app->request->queryParams = [
            'page' => $this->_page
        ];
        $pages = new Pagination([
            'totalCount' => $this->_totalCount,
            'pageSize' => $this->_perPage
        ]);
        $models = $query->offset($pages->offset)
            ->limit($pages->limit)
            ->all();
        $this->session->set('totalCountPage', $this->_totalCountPage);
        return $models;
    }

    private function setPrevPage(): void
    {
        $page = $this->session->get('page');
        $this->_page = $page - 1;
        if ($this->_page <= 0) {
            $this->_page = $this->session->get('totalCountPage');
        }
        $this->session->set('page', $this->_page);
    }

    /**
     * @return mixed
     */
    private function setNextPage(): void
    {
        $totalCountPage = $this->session->get('totalCountPage');
        $page = $this->session->get('page');
        if (!$page) {
            $page = 1;
        }
        $this->_page = $page + 1;
        if ($totalCountPage < $this->_page) {
            $this->_page = 1;
        }
        if ($totalCountPage >= $this->_page) {
            $this->session->set('page', $this->_page);
        }
    }
}
