<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use Doctrine\DBAL\Driver\Connection;

class LoggerController extends AbstractController
{
  /**
   * The request
   */
  public $request;
    /**
     * @Route("/logger", defaults={"page": "1", "_format"="html"}, methods={"GET"}, name="logger")
     */
    public function index(Connection $connection, Request $request)
    {
      $this->request = $request;


      $schemaManager = $connection->getSchemaManager();
      if ($schemaManager->tablesExist(array('log')) != true) {
        return $this->render('logger/no-table.html.twig', [
            'controller_name' => 'LoggerController',
        ]);
      }


      if(empty((int)$this->request->query->get('page'))){
        $page = 0;
      } else {
        $page = (int)$this->request->query->get('page');
      }

      $offset = $page * 100;

      $type = (int)$this->request->query->get('type');
      if($type !== 0 and empty($type)){
        $type = false;
      }

      $logger = array(
        'sortDate' => $this->request->query->get('sort-date') == 'desc' ? 'desc' : 'asc',
        'type' => $type,
        'urls' => array(
          'types' => $this->getLinkTypes(),
          'page_next' => $this->getLinkNextPage(),
          'page_back' => $this->getLinkBackPage(),
          'sortDate' => array(
            'desc' => $this->getLinkSortDateDesc(),
            'asc' => $this->getLinkSortDateAsc(),
          ),
        )
      );

      $sql = 'SELECT * FROM log';

      if($logger['type'] !== false){
        $sql .= sprintf(' WHERE type = %s', $logger['type']);
      }

      if($logger['sortDate'] == 'desc'){
        $sql .= sprintf(' ORDER BY %s', 'ts DESC');
      } else {
        $sql .= sprintf(' ORDER BY %s', 'ts ASC');
      }

      $sql .= sprintf(' LIMIT %s', 100);
      $sql .= sprintf(' OFFSET %s', $offset);

      dump($sql);

      $logger['rows'] = $connection->fetchAll($sql);
      if(empty($logger['rows'])){
        $logger['rows'] = false;
      }

      return $this->render('logger/index.html.twig', [
          'controller_name' => 'LoggerController',
          'log' => $logger,
          'page' => $page,
      ]);
    }

    /**
     * Get link for select types
     */
    private function getLinkTypes()
    {
      $uri = $this->request->getUri();
      $url_parts = parse_url($uri);

      if(empty($url_parts['query'])){
        $url_data_array = array();
      } else {
        parse_str($url_parts['query'], $url_data_array);
      }

      $urls = array();
      for ($i=0; $i < 10; $i++) {
        $url_data_array['type'] = $i;
        $url_query_new = http_build_query($url_data_array);
        if( ! empty($url_query_new)){
          $url_query_new = '?' . $url_query_new;
        }

        $url_new = $url_parts['scheme'] . '://' . $url_parts['host'] . $url_parts['path'] . $url_query_new;

        $urls[] = array(
          'name' => $i,
          'url' => $url_new
        );
      }

      return $urls;
    }

    /**
     * Get link next page table
     */
    private function getLinkNextPage()
    {
      $uri = $this->request->getUri();
      $url_parts = parse_url($uri);
      if(empty($url_parts['query'])){
        $url_data_array = array();
      } else {
        parse_str($url_parts['query'], $url_data_array);
      }

      if(empty($url_data_array['page'])){
        $url_data_array['page'] = 1;
      } else {
        $url_data_array['page']++;
      }

      $url_query_new = http_build_query($url_data_array);
      if( ! empty($url_query_new)){
        $url_query_new = '?' . $url_query_new;
      }

      $url_new = $url_parts['scheme'] . '://' . $url_parts['host'] . $url_parts['path'] . $url_query_new;

      return $url_new;
    }

    /**
     * Get link sort date desc
     */
    private function getLinkSortDateDesc()
    {
      $uri = $this->request->getUri();
      $url_parts = parse_url($uri);
      if(empty($url_parts['query'])){
        $url_data_array = array();
      } else {
        parse_str($url_parts['query'], $url_data_array);
      }

      $url_data_array['sort-date'] = 'desc';

      $url_query_new = http_build_query($url_data_array);
      if( ! empty($url_query_new)){
        $url_query_new = '?' . $url_query_new;
      }

      $url_new = $url_parts['scheme'] . '://' . $url_parts['host'] . $url_parts['path'] . $url_query_new;

      return $url_new;
    }

    /**
     * Get link sort date desc
     */
    private function getLinkSortDateAsc()
    {
      $uri = $this->request->getUri();
      $url_parts = parse_url($uri);
      if(empty($url_parts['query'])){
        $url_data_array = array();
      } else {
        parse_str($url_parts['query'], $url_data_array);
      }

      $url_data_array['sort-date'] = 'asc';

      $url_query_new = http_build_query($url_data_array);
      if( ! empty($url_query_new)){
        $url_query_new = '?' . $url_query_new;
      }

      $url_new = $url_parts['scheme'] . '://' . $url_parts['host'] . $url_parts['path'] . $url_query_new;

      return $url_new;
    }

    /**
     * Get link back page table
     */
    private function getLinkBackPage()
    {
      $uri = $this->request->getUri();
      $url_parts = parse_url($uri);

      if(empty($url_parts['query'])){
        $url_data_array = array();
      } else {
        parse_str($url_parts['query'], $url_data_array);
      }

      if(empty($url_data_array['page'])){
        unset($url_data_array['page']);
      } else {
        $url_data_array['page'] = $url_data_array['page']-1;
        if(empty($url_data_array['page'])){
          unset($url_data_array['page']);
        }
      }

      $url_query_new = http_build_query($url_data_array);
      if( ! empty($url_query_new)){
        $url_query_new = '?' . $url_query_new;
      }

      $url_new = $url_parts['scheme'] . '://' . $url_parts['host'] . $url_parts['path'] . $url_query_new;

      return $url_new;
    }
}
