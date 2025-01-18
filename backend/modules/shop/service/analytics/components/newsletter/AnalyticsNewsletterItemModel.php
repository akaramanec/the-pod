<?php

namespace backend\modules\shop\service\analytics\components\newsletter;

use yii\helpers\Url;

abstract class AnalyticsNewsletterItemModel implements AnalyticsNewsletterItemInterface
{
    /**
     * @var string $title
     */
    private $title;

    /** @var int $value */
    private $value;

    /** @var string $createNewsletterLink */
    private $createNewsletterLink;

    /** @var array $getParams */
    private $getParams = [
        'tag' => '',
        'dateFrom' => '',
        'dateTo' => ''
    ];

    /**
     * @param string $key
     * @param string $value
     */
    public function setGetParams(string $key, string $value)
    {
        if (isset($this->getParams[$key])) {
            $this->getParams[$key] = $value;
        }
    }

    /**
     * @param int $countManager
     * @param string $dateFrom
     * @param string $dateTo
     */
    public function __construct(int $countManager, string $dateFrom, string $dateTo)
    {
        $this->setValue($countManager);
        $this->setNewsletterLink('/customer/newsletter/create-by-analytics');
        $this->setGetParams('dateFrom', $dateFrom);
        $this->setGetParams('dateTo', $dateTo);
        $this->init();
    }


    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title)
    {
        $this->title = $title;
    }

    /**
     * @return float
     */
    public function getValue(): float
    {
        return $this->value;
    }

    /**
     * @param float $value
     */
    public function setValue(float $value)
    {
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getNewsletterLink(): string
    {

        return $this->createNewsletterLink."?".http_build_query($this->getParams);
    }

    /**
     * @param string $createNewsletterLink
     */
    public function setNewsletterLink(string $createNewsletterLink)
    {
        $this->createNewsletterLink = $createNewsletterLink;
    }

    /**
     * @return string
     */
    public final function getCartHtml(): string
    {
        return <<<HTML
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">
                            {$this->htmlCartHeaderTitle()}
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="card-title">
                            {$this->htmlCartBodyTitle()}
                        </div>
                    </div>
                </div>
HTML;

    }

    /**
     * @return string
     */
    protected function htmlCartHeaderTitle(): string
    {
        return <<<HTML
 <span class="text-title">{$this->getTitle()}</span>
HTML;

    }

    /**
     * @return string
     */
    protected function htmlCartBodyTitle(): string
    {
        return <<<HTML
<p>
    <span class="text-value">{$this->getValue()}</span>
</p>
<p>
    <span class="text-dimension">
        <a href="{$this->getNewsletterLink()}">Создать рассылку</a>
    </span>
</p>
HTML;

    }

}