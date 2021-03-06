<?php

/**
 * Solr Connection Class
 */
class Solr
{
    // for pagination
    private $_limit = 3; // total per page
    private $_page;
    private $_total;

    protected $client;

    protected $_connection = null;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->client = new \Solarium\Client(Config::get('solr'));
    }

    /**
     * Get all stores from Solr.
     *
     * @param int $page
     * @param int $limit
     *
     * @return array
     */
    function getAll($page = 1, $limit = 5)
    {
        $stores = [];
        $result = [];
        $facets = [];

        // for pagination
        $this->_page = $page;
        $this->_limit = $limit;
        $start = ($this->_page == 1 || $this->_page == 0) ? 0 : ($this->_limit * ($this->_page - 1));

        $facetQuery = "&facet=true&facet.field=category&facet.field=state&facet.field=town&json.nl=map";
        $query = 'q=*:*&wt=json&indent=true' . $facetQuery . '&rows=' . $this->_limit . '&start=' . $start . '&sort=rank asc';

        $url = self::getSolrUrl() . $query;

        $client = new \Guzzle\Service\Client($url);
        $request = $client->get();
        $response = $request->send();
        $resultSet = $response->json();

        foreach ($resultSet['response']['docs'] as $solrDocument) {
            $stores[] = $solrDocument;
        }

        foreach ($resultSet['facet_counts']['facet_fields'] as $facetname => $facetValues) {
            $facetList = [];
            foreach ($facetValues as $facetKey => $facetCount) {
                $facetList[$facetKey] = $facetCount;
            }

            $facets[$facetname] = $facetList;
        }

        $result['result'] = $stores;
        $result['facets'] = $facets;
        $this->_total = $resultSet['response']['numFound']; // for pagination
        $result['total'] = $resultSet['response']['numFound'];

        return $result;
    }

    /**
     * Get stores by query filter.
     *
     * @param $fields
     * @param $page
     * @param $limit
     *
     * @return array
     */
    public function getAllByQuery($fields, $page = 1, $limit = 5)
    {
        $stores = [];
        $result = [];
        $facets = [];
        $fq = null;

        if (!empty($fields['category'])) {
            $fq = '&fq=category:"' . $fields['category'] . '"';
        }

        if (!empty($fields['state'])) {
            $fq .= '&fq=state:"' . $fields['state'] . '"';
        }

        if (!empty($fields['town'])) {
            $fq .= '&fq=town:"' . $fields['town'] . '"';
        }

        if (!empty($fields['shop'])) {
            $fq .= '&fq=name:"' . $fields['shop'] . '"';
        }

        if (!empty($fields['keyword'])) {
            $fq .= '&fq=keyword:"' . $fields['keyword'] . '"';
        }

        // for pagination
        $this->_page = $page;
        $this->_limit = $limit;
        $start = ($this->_page == 1 || $this->_page == 0) ? 0 : ($this->_limit * ($this->_page - 1));

        $facetQuery = "&facet=true&facet.field=category&facet.field=state&facet.field=town&json.nl=map&facet.mincount=1";
        $query = 'q=*:*&wt=json&indent=true' . $facetQuery . $fq . '&rows=' . $this->_limit . '&start=' . $start . '&sort=rank asc';

        $url = self::getSolrUrl() . $query;

        $client = new \Guzzle\Service\Client($url);
        $request = $client->get();
        $response = $request->send();
        $resultSet = $response->json();

        foreach ($resultSet['response']['docs'] as $solrDocument) {
            $stores[] = $solrDocument;
        }

        foreach ($resultSet['facet_counts']['facet_fields'] as $facetname => $facetValues) {
            $facetList = [];
            foreach ($facetValues as $facetKey => $facetCount) {
                $facetList[$facetKey] = $facetCount;
            }

            $facets[$facetname] = $facetList;
        }

        $result['result'] = $stores;
        $result['facets'] = $facets;
        $this->_total = $resultSet['response']['numFound']; // for pagination
        $result['total'] = $resultSet['response']['numFound'];

        return $result;
    }

    function getAllByGeo($lat, $long, $distance, $page = 1, $limit = 5)
    {
        $stores = [];
        $result = [];
        $facets = [];

        // for pagination
        $this->_page = $page;
        $this->_limit = $limit;
        $start = ($this->_page == 1 || $this->_page == 0) ? 0 : ($this->_limit * ($this->_page - 1));

        $geoQuery = "&fq={!bbox}&sfield=shop_location&pt=$lat,$long&d=$distance";
        $facetQuery = "&facet=true&facet.field=category&facet.field=state&facet.field=town&json.nl=map&facet.mincount=1";

        $query = "q=*:*&wt=json&indent=true" . $facetQuery . $geoQuery . '&rows=' . $this->_limit . '&start=' . $start;
        $url = $this->getSolrUrl() . $query;

        $resultSet = json_decode($this->httpGet($url));

        foreach ($resultSet->response->docs as $solrDocument) {

            $stores[] = (array)$solrDocument;
        }

        foreach ($resultSet->facet_counts->facet_fields as $facetname => $facetValues) {
            $facetList = [];
            foreach ($facetValues as $facetKey => $facetCount) {
                $facetList[$facetKey] = $facetCount;
            }

            $facets[$facetname] = (array)$facetList;
        }

        $result['result'] = $stores;
        $result['facets'] = $facets;
        $this->_total = $resultSet->response->numFound; // for pagination
        $result['total'] = $resultSet->response->numFound;

        return $result;
    }

    function httpGet($url)
    {

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);

        $output = curl_exec($ch);

        curl_close($ch);
        return $output;
    }

    /**
     * Index Document to Solr.
     *
     * @param $id
     *
     * @return \Solarium\QueryType\Update\Result
     */
    public function save($id)
    {
        $shop = Shop::find($id);

        // create a solr connection
        $client = $this->solrConnection();

        $updateDOcument = $client->createUpdate();
        $shopDocument = $updateDOcument->createDocument();
        $shopDocument->id = $shop->id;
        $shopDocument->name = $shop->name;
        $shopDocument->category = $shop->category;
        $shopDocument->state = $shop->state;
        $shopDocument->town = $shop->town;
        $shopDocument->town_location = "$shop->town_latitude,$shop->town_longitude";
        $shopDocument->address = $shop->address;
        $shopDocument->tel = $shop->tel;
        $shopDocument->fax = $shop->fax;
        $shopDocument->cperson = $shop->cperson;
        $shopDocument->mobile = $shop->mobile;
        $shopDocument->email = $shop->email;
        $shopDocument->urlcom = $shop->description;
        $shopDocument->urladv = $shop->urlcom;
        $shopDocument->description = $shop->urladv;
        $shopDocument->rank = $shop->rank;
        $shopDocument->shop_location = "$shop->latitude,$shop->longitude";

        $updateDOcument->addDocument($shopDocument);
        $updateDOcument->addCommit();

        $result = $client->update($updateDOcument);

        return $result;
    }

    public function delete($id)
    {
        try {
            $connection = $this->solrConnection();
            $deleteShop = $connection->createUpdate();
            $deleteShop->addDeleteById($id);
            $deleteShop->addCommit();

            $connection->update($deleteShop);
        } catch(\Solarium\Exception\HttpException $e) {
            throw new Exception ("Unable to delete shop from Solr");
        }
    }

    /**
     * Create custom pagination link.
     *
     * @param $links
     * @param $list_class
     *
     * @return string
     */
    public function createLinks($links, $list_class)
    {
        if ($this->_limit == 'all') {
            return '';
        }

        $last = ceil($this->_total / $this->_limit);

        $start = (($this->_page - $links) > 0) ? $this->_page - $links : 1;
        $end = (($this->_page + $links) < $last) ? $this->_page + $links : $last;

        $html = '<ul class="' . $list_class . '">';

        $class = ($this->_page == 1) ? "disabled" : "";
        $html .= '<li class="' . $class . '"><a href="?limit=' . $this->_limit . '&page=' . ($this->_page - 1) . '">&laquo;</a></li>';

        if ($start > 1) {
            $html .= '<li><a href="?limit=' . $this->_limit . '&page=1">1</a></li>';
            $html .= '<li class="disabled"><span>...</span></li>';
        }

        for ($i = $start; $i <= $end; $i++) {
            $class = ($this->_page == $i) ? "active" : "";
            $html .= '<li class="' . $class . '"><a href="?limit=' . $this->_limit . '&page=' . $i . '">' . $i . '</a></li>';
        }

        if ($end < $last) {
            $html .= '<li class="disabled"><span>...</span></li>';
            $html .= '<li><a href="?limit=' . $this->_limit . '&page=' . $last . '">' . $last . '</a></li>';
        }

        $class = ($this->_page == $last) ? "disabled" : "";
        $html .= '<li class="' . $class . '"><a href="?limit=' . $this->_limit . '&page=' . ($this->_page + 1) . '">&raquo;</a></li>';

        $html .= '</ul>';

        return $html;
    }

    /**
     * Get Solr URL.
     *
     * @return string
     */
    private static function getSolrUrl()
    {
        $uri = Config::get('solr')['host'] . ':' . Config::get('solr')['port'] . Config::get(
                'solr'
            )['path'] . Config::get('solr')['core'];
        $url = 'http://' . $uri . '/select?';

        return $url;
    }

    /**
     * Solr Connection.
     *
     * @return \Solarium\Client
     * @throws Exception
     */
    private function solrConnection()
    {
        $this->_connection = new Solarium\Client(
            array(
                'endpoint' => array(
                    'adapteroptions' => array(
                        'host'    => Config::get('solr')['host'],
                        'port'    => Config::get('solr')['port'],
                        'path'    => Config::get('solr')['path'],
                        'core'    => Config::get('solr')['core'],
                        //Create core with country code. Example: For Malaysia, Solr core is 'my'
                        'timeout' => 15
                    )
                )
            )
        );

        $ping = $this->_connection->createPing();
        try {
            $this->_connection->ping($ping);
        } catch(\Solarium\Exception\HttpException $e) {
            throw new Exception("Unable to connect to Solr");
        }

        return $this->_connection;
    }
} 