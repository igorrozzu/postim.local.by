<?php

namespace app\commands\cron\siteMap\models;

use Yii;

class SiteMap {


    const ALWAYS = 'always';
    const HOURLY = 'hourly';
    const DAILY = 'daily';
    const WEEKLY = 'weekly';
    const MONTHLY = 'monthly';
    const YEARLY = 'yearly';
    const NEVER = 'never';

    protected $items = [];
    protected $sections = [];



    /**
     * @param $url
     * @param string $changeFreq
     * @param float $priority
     * @param int $lastmod
     */
    public function addUrl($url, $changeFreq=self::DAILY, $priority=0.5, $lastMod=0)
    {
        $host =  Yii::$app->params['site.hostName'];
        $item = array(
            'loc' => $host . $url,
            'changefreq' => $changeFreq,
            'priority' => $priority
        );
        if ($lastMod)
            $item['lastmod'] = $this->dateToW3C($lastMod);

        $this->items[] = $item;
    }


    public function addModels($models, $changeFreq=self::DAILY, $priority=0.5)
    {
        $host =  Yii::$app->params['site.hostName'];
        foreach ($models as $model)
        {
            $item = array(
                'loc' => $host . $model->getUrl(),
                'changefreq' => $changeFreq,
                'priority' => $priority
            );

            if ($model->hasAttribute('date'))
                $item['lastmod'] = $this->dateToW3C($model->date);


            $this->items[] = $item;

            if($model->hasMethod('getUrls')){
                $urls = $model->getUrls();
                foreach ($urls as $url){

                    $item = array(
                        'loc' => $host . $url,
                        'changefreq' => $changeFreq,
                        'priority' => $priority
                    );

                    $this->items[] = $item;
                }
            }


        }
    }

    public function addSections ($items){
        foreach ($items as $item)
        {
            if (isset($item['lastmod']))
                $item['lastmod'] = $this->dateToW3C($item['lastmod']);

            $this->sections[] = $item;
        }
    }


    /**
     * @return string XML code
     */
    public function renderSections()
    {
        $dom = new \DOMDocument('1.0', 'utf-8');
        $urlset = $dom->createElement('sitemapindex');
        $urlset->setAttribute('xmlns','http://www.sitemaps.org/schemas/sitemap/0.9');
        $urlset->setAttribute('xmlns:xsi','http://www.w3.org/2001/XMLSchema-instance');
        $urlset->setAttribute('xsi:schemaLocation','http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/siteindex.xsd');

        foreach($this->sections as $item)
        {
            $url = $dom->createElement('sitemap');

            foreach ($item as $key=>$value)
            {
                $elem = $dom->createElement($key);
                $elem->appendChild($dom->createTextNode($value));
                $url->appendChild($elem);
            }

            $urlset->appendChild($url);
        }
        $dom->appendChild($urlset);

        return $dom->saveXML();
    }



    /**
     * @return string XML code
     */
    public function render()
    {
        $dom = new \DOMDocument('1.0', 'utf-8');
        $urlset = $dom->createElement('urlset');
        $urlset->setAttribute('xmlns','http://www.sitemaps.org/schemas/sitemap/0.9');
        $urlset->setAttribute('xmlns:xsi','http://www.w3.org/2001/XMLSchema-instance');
        $urlset->setAttribute('xmlns:xhtml','http://www.w3.org/1999/xhtml');
        $urlset->setAttribute('xsi:schemaLocation','http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd');

        foreach($this->items as $item)
        {
            $url = $dom->createElement('url');

            foreach ($item as $key=>$value)
            {
                $elem = $dom->createElement($key);
                $elem->appendChild($dom->createTextNode($value));
                $url->appendChild($elem);
            }

            $urlset->appendChild($url);
        }
        $dom->appendChild($urlset);

        return $dom->saveXML();
    }



    protected function dateToW3C($date)
    {
        if (is_int($date))
            return date(DATE_W3C, $date);
        else
            return date(DATE_W3C, strtotime($date));
    }


}