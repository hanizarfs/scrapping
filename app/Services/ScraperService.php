<?php

namespace App\Services;

use GuzzleHttp\Client;

class ScraperService
{
    protected $client;

    public function __construct()
    {
        $this->client = new Client();
    }

    public function scrape($url)
    {
        // Mengirim permintaan GET ke URL
        $response = $this->client->request('GET', $url);

        // Mengambil konten HTML dari respons
        $html = $response->getBody()->getContents();

        // Mengolah konten HTML
        return $this->parseHtml($html);
    }

    protected function parseHtml($html)
    {
        $dom = new \DOMDocument();
        @$dom->loadHTML($html); // '@' untuk mengabaikan peringatan HTML yang tidak valid

        $xpath = new \DOMXPath($dom);

        // Mengambil data dari elemen profil
        $profileData = [
            'name' => $this->extractText($xpath, '//div[@id="gsc_prf_i"]//div[@id="gsc_prf_in"]'),
            'affiliation' => $this->extractText($xpath, '//div[@id="gsc_prf_i"]//div[@class="gsc_prf_il"]'),
            'email_verified' => $this->extractText($xpath, '//div[@id="gsc_prf_i"]//div[@id="gsc_prf_ivh"]'),
            'interests' => $this->extractText($xpath, '//div[@id="gsc_prf_i"]//div[@id="gsc_prf_int"]'),
            'photo_url' => $this->extractAttribute($xpath, '//div[@id="gsc_prf_pu"]//img', 'src')
        ];

        // Mengambil data dari elemen "Cited by"
        $citedByData = [
            'citations_all' => $this->extractTableCell($xpath, '//table[@id="gsc_rsb_st"]//tr[1]//td[2]'),
            'citations_since_2019' => $this->extractTableCell($xpath, '//table[@id="gsc_rsb_st"]//tr[1]//td[3]'),
            'h_index_all' => $this->extractTableCell($xpath, '//table[@id="gsc_rsb_st"]//tr[2]//td[2]'),
            'h_index_since_2019' => $this->extractTableCell($xpath, '//table[@id="gsc_rsb_st"]//tr[2]//td[3]'),
            'i10_index_all' => $this->extractTableCell($xpath, '//table[@id="gsc_rsb_st"]//tr[3]//td[2]'),
            'i10_index_since_2019' => $this->extractTableCell($xpath, '//table[@id="gsc_rsb_st"]//tr[3]//td[3]')
        ];

        // Mengambil data grafik tahun dan jumlah artikel
        $chartData = $this->extractChartData($xpath);

        return [
            'profile' => $profileData,
            'cited_by' => $citedByData,
            'chart' => $chartData
        ];
    }

    private function extractText($xpath, $query)
    {
        $nodes = $xpath->query($query);
        return $nodes->length > 0 ? $nodes->item(0)->nodeValue : null;
    }

    private function extractAttribute($xpath, $query, $attribute)
    {
        $nodes = $xpath->query($query);
        return $nodes->length > 0 ? $nodes->item(0)->getAttribute($attribute) : null;
    }

    private function extractTableCell($xpath, $query)
    {
        $nodes = $xpath->query($query);
        return $nodes->length > 0 ? $nodes->item(0)->nodeValue : null;
    }

    private function extractChartData($xpath)
    {
        $years = $xpath->query('//div[@class="gsc_md_hist_b"]//span[@class="gsc_g_t"]');
        $counts = $xpath->query('//div[@class="gsc_md_hist_b"]//a[@class="gsc_g_a"]//span[@class="gsc_g_al"]');

        $chartData = [];
        foreach ($years as $index => $year) {
            $yearText = trim($year->nodeValue);
            $count = $counts->length > $index ? trim($counts->item($index)->nodeValue) : '0';
            $chartData[] = ['year' => $yearText, 'count' => $count];
        }

        return $chartData;
    }
}
