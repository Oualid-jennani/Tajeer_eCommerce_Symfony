<?php


namespace App\Form\Model;


use App\Entity\Category;

class SearchProductsByCategory
{
    /**
     * @var string
     */
    protected string $productName;

    /**
     * @return string
     */
    public function getProductName(): string
    {
        return $this->productName;
    }

    /**
     * @param string $productName
     */
    public function setProductName(string $productName): void
    {
        $this->productName = $productName;
    }
}