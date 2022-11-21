<?php

class Pagination
{

    public function __construct()
    {

    }

    private $currentPage;
    private $pages;
    private $nResults;
    private $resultsForPage;
    private $index;

    public function getResultsForPage()
    {
        return $this->resultsForPage;
    }

    public function getCurrentPage()
    {
        return $this->currentPage - 1;
    }

    public function getNResults()
    {
        return $this->nResults;
    }

    public function getTotalPages()
    {
        return $this->pages;
    }

    public function setConfig($resultsForPage, $totalRecords, $currentPage)
    {
        $this->resultsForPage = $resultsForPage;
        $this->index = 0;
        $this->currentPage = 1;

        $this->nResults = $totalRecords;
        $this->pages = intval(round($this->nResults / $this->resultsForPage));

        $this->currentPage = (is_numeric($currentPage) == true) ? intval($currentPage) : 1;

        $this->currentPage = (intval($this->currentPage) >= 1) ? intval($this->currentPage) : 1;

        $this->index = ($this->currentPage - 1) * ($this->resultsForPage);

        return $this->index . ',' . $this->resultsForPage;
    }

}
