<?php
class Sitemap extends CI_Controller{
    private $xmlDoc;
    
    /**
     * post model
     * @var \Posts_Model 
     */
    public $Posts_Model;
    
    /**
     * file_categories model
     * @var \File_Categories
     */
    public $File_Categories;

    public function __construct()
    {
        parent::__construct();
        session_start();
        
        $this->load->model('Posts_Model');
        $this->load->model('File_Categories');
    }
    public function index(){
        $base_url = base_url();
        $data = array();
        
        $posts = array(
            'news' => array('url' => 'news/', 'priority' => '0.8'),
            'articles' => array('url' => 'articles/', 'priority' => '0.7'),
            'reviews' => array('url' => 'reviews/', 'priority' => '0.8'),
        );

        foreach ($posts as $prefix => $it){
            $category = $this->File_Categories->get_by_field('prefix', $prefix);
            if(element('file_category_id', $category))
                $data[$prefix] = $this->Posts_Model->search (array(
                    'file_category_id' => $category['file_category_id'],
                    'status' => MY_Model::STATUS_ACTIVE,
                ));
        }

        $data['objects'] = $this->db->where('status',1)->get('main_object')->result();

        $this->xmlDoc = new DOMDocument( "1.0", "UTF-8" );

        $root = $this->xmlDoc->appendChild( $this->xmlDoc->createElement( 'urlset' ) );
        $root->appendChild( $this->xmlDoc->createAttribute( 'xmlns' ) )->appendChild( $this->xmlDoc->createTextNode( 'http://www.google.com/schemas/sitemap/0.84' ) );

        $this->createUrlElement( $root, $base_url, date( 'Y-m-d' ), 'daily', '0.5' );
        $this->createUrlElement( $root, $base_url . 'catalog/', date( 'Y-m-d' ), 'daily', '0.5' );
        $this->createUrlElement( $root, $base_url . 'news/', date( 'Y-m-d' ), 'daily', '0.5' );
        $this->createUrlElement( $root, $base_url . 'articles/', date( 'Y-m-d' ), 'daily', '0.5' );
        $this->createUrlElement( $root, $base_url . 'about/', date( 'Y-m-d' ), 'weekly', '0.5' );
        $this->createUrlElement( $root, $base_url . 'publicity/', date( 'Y-m-d' ), 'weekly', '0.5' );

//        foreach( $data['news'] as $news ) $this->createUrlElement( $root, $base_url . 'news/' . $news->alias, date( 'Y-m-d', strtotime( $news->date ) ), 'daily', '0.8' );
//        foreach( $data['article'] as $article ) $this->createUrlElement( $root, $base_url . 'articles/' . $article->alias, date( 'Y-m-d', strtotime( $article->date ) ), 'daily', '0.7' );
//        
        foreach ($posts as $prefix => $it){
            if(element($prefix, $data))
                foreach( $data[$prefix] as $item ) 
                    $this->createUrlElement( $root, $base_url . $it['url'] . $item['alias'], date( 'Y-m-d', strtotime( $item['updated']) ), 'weekly', $it['priority'] );
        }
        
        foreach( $data['objects'] as $object ) $this->createUrlElement( $root, $base_url . 'catalog/' . $object->alias, date( 'Y-m-d' ), 'daily', '0.9' );

        $this->xmlDoc->formatOutput = true;
        echo (int) $this->xmlDoc->save( "sitemap.xml" );
    }

    private function createUrlElement( &$parent, $loc, $lastmod, $changefreq, $priority )
    {
        $url = $parent->appendChild( $this->xmlDoc->createElement( 'url' ) );
        $url->appendChild( $this->xmlDoc->createElement( 'loc', $loc ) );
        $url->appendChild( $this->xmlDoc->createElement( 'lastmod', $lastmod ) );
        $url->appendChild( $this->xmlDoc->createElement( 'changefreq', $changefreq ) );
        $url->appendChild( $this->xmlDoc->createElement( 'priority', $priority ) );
    }
}