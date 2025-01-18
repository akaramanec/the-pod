<?php

namespace backend\modules\shop\service\analytics\components\general;

abstract class AnalyticsGeneralItemModel implements AnalyticsGeneralItemInterface
{
    /**
     * @var string $title
     */
    private $title;

    /** @var float $value */
    private $value;

    /** @var string $dimension */
    private $dimension;

    /** @var string */
    private $description;


    public function __construct()
    {
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
    public function getDimension(): string
    {
        return $this->dimension;
    }

    /**
     * @param string $dimension
     */
    public function setDimension(string $dimension)
    {
        $this->dimension = $dimension;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description)
    {
        $this->description = $description;
    }

    /**
     * @return string
     */
    public final function getCartHtml(): string
    {
        return <<<HTML
                <div class="card">
                    <i class="fas fa-info-circle general-info-icon" title="{$this->getDescription()}"></i>
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
<span class="text-value">{$this->getValue()}</span>
<span class="text-dimension"><p>{$this->getDimension()}</p></span>
HTML;

    }
}