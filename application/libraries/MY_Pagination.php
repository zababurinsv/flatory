<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * MY_Pagination
 *
 * @author Valery Shusharin <shusharin_valery@mail.ru>
 */
class MY_Pagination extends CI_Pagination {

    /**
     * Super object CodeIgniter
     * @var \MY_Controller
     */
    protected $_CI;
    protected $is_show_all = true;
    protected $is_show_limit_select = true;

    /**
     *
     * @var array 
     */
    protected $pagination_limits = [10, 25, 50, 100];

    /**
     *
     * @var int 
     */
    protected $pagination_limit = 10;

    public function __construct($params = array()) {

        $this->first_link = '«';
        $this->next_link = '>';
        $this->prev_link = '<';
        $this->last_link = '»';

        if (array_get($params, 'controller') && $params['controller'] instanceof MY_Controller) {
            $this->_CI = $params['controller'];
        } else {
            $this->_CI = &get_instance();
        }

        if (!isset($this->_CI->template_dir))
            $this->_CI->template_dir = '';


//        vdump($user_limit);
//        vdump($this->_CI->uri->uri_string());
//        vdump($this->_CI->get_user_settings());
    }

    /**
     * initialize pagination
     * @param array $params :<br>
     * <b>base_url</b> - string ;<br>
     * <b>total_rows</b> - int ;<br>
     * <b>per_page</b> - int ;<br>
     * <b>page_query_string</b> - bool ;<br>
     * <b>is_show_all</b> - bool ;<br>
     * <b>is_show_limit_select</b> - bool ;<br>
     * @return \MY_Pagination
     */
    public function initialize($params = array()) {
        parent::initialize($params);

        return $this;
    }

    public function create_links() {
                
        // If our item count or per-page total is zero there is no need to continue.
        if ($this->total_rows == 0 OR $this->per_page == 0) {
            return '';
        }

        // Calculate the total number of pages
        $num_pages = ceil($this->total_rows / $this->per_page);

        // Is there only one page? Hm... nothing more to do here then.
        if ($num_pages == 1) {
            return '';
        }

        // Set the base page index for starting page number
        if ($this->use_page_numbers) {
            $base_page = 1;
        } else {
            $base_page = 0;
        }

        // Determine the current page number.
        if ($this->_CI->config->item('enable_query_strings') === TRUE OR $this->page_query_string === TRUE) {
            if ($this->_CI->input->get($this->query_string_segment) != $base_page) {
                $this->cur_page = $this->_CI->input->get($this->query_string_segment);

                // Prep the current page - no funny business!
                $this->cur_page = (int) $this->cur_page;
            }
        } else {
            if ($this->_CI->uri->segment($this->uri_segment) != $base_page) {
                $this->cur_page = $this->_CI->uri->segment($this->uri_segment);

                // Prep the current page - no funny business!
                $this->cur_page = (int) $this->cur_page;
            }
        }

        // Set current page to 1 if using page numbers instead of offset
        if ($this->use_page_numbers AND $this->cur_page == 0) {
            $this->cur_page = $base_page;
        }

        $this->num_links = (int) $this->num_links;

        if ($this->num_links < 1) {
            show_error('Your number of links must be a positive number.');
        }

        if (!is_numeric($this->cur_page)) {
            $this->cur_page = $base_page;
        }

        // Is the page number beyond the result range?
        // If so we show the last page
        if ($this->use_page_numbers) {
            if ($this->cur_page > $num_pages) {
                $this->cur_page = $num_pages;
            }
        } else {
            if ($this->cur_page > $this->total_rows) {
                $this->cur_page = ($num_pages - 1) * $this->per_page;
            }
        }

        $uri_page_number = $this->cur_page;

        if (!$this->use_page_numbers) {
            $this->cur_page = floor(($this->cur_page / $this->per_page) + 1);
        }

        // Calculate the start and end numbers. These determine
        // which number to start and end the digit links with
        $start = (($this->cur_page - $this->num_links) > 0) ? $this->cur_page - ($this->num_links - 1) : 1;
        $end = (($this->cur_page + $this->num_links) < $num_pages) ? $this->cur_page + $this->num_links : $num_pages;

        // Is pagination being used over GET or POST?  If get, add a per_page query
        // string. If post, add a trailing slash to the base URL if needed
        if ($this->_CI->config->item('enable_query_strings') === TRUE OR $this->page_query_string === TRUE) {
            $this->base_url = rtrim($this->base_url) . '&amp;' . $this->query_string_segment . '=';
        } else {
            $this->base_url = rtrim($this->base_url, '/') . '/';
        }

        // And here we go...
        $output = [];

        // Render the "First" link
        if ($this->first_link !== FALSE AND $this->cur_page > ($this->num_links + 1)) {
            $output[] = [
                'url' => ($this->first_url == '') ? $this->base_url : $this->first_url,
                'title' => $this->first_link,
            ];
        }

        // Render the "previous" link
        if ($this->prev_link !== FALSE AND $this->cur_page != 1 && $uri_page_number !== -1) {
            if ($this->use_page_numbers) {
                $i = $uri_page_number - 1;
            } else {
                $i = $uri_page_number - $this->per_page;
            }

            if ($i == 0 && $this->first_url != '') {
                $output[] = [
                    'url' => $this->first_url,
                    'title' => $this->prev_link,
                ];
            } else {
                $i = ($i == 0) ? '' : $this->prefix . $i . $this->suffix;
                $output[] = [
                    'url' => $this->base_url . $i,
                    'title' => $this->prev_link,
                ];
            }
        }

        // Render the pages
        if ($this->display_pages !== FALSE) {
            // Write the digit links
            for ($loop = $start - 1; $loop <= $end; $loop++) {
                if ($this->use_page_numbers) {
                    $i = $loop;
                } else {
                    $i = ($loop * $this->per_page) - $this->per_page;
                }

                if ($i >= $base_page) {
                    if ($this->cur_page == $loop) {
                        // Current page
                        $output[] = [
                            'url' => 'javascript:void(0)',
                            'title' => $loop,
                            'current' => TRUE,
                        ];
                    } else {
                        $n = ($i == $base_page) ? '' : $i;

                        if ($n == '' && $this->first_url != '') {
                            $output[] = [
                                'url' => $this->first_url,
                                'title' => $loop,
                            ];
                        } else {
                            $n = ($n == '') ? '' : $this->prefix . $n . $this->suffix;

                            $output[] = [
                                'url' => $this->base_url . $n,
                                'title' => $loop,
                            ];
                        }
                    }
                }
            }
        }

        // Render the "next" link
        if ($this->next_link !== FALSE AND $this->cur_page < $num_pages) {
            if ($this->use_page_numbers) {
                $i = $this->cur_page + 1;
            } else {
                $i = ($this->cur_page * $this->per_page);
            }

            $output[] = [
                'url' => $this->base_url . $this->prefix . $i . $this->suffix,
                'title' => $this->next_link,
            ];
        }

        // Render the "Last" link
        if ($this->last_link !== FALSE AND ( $this->cur_page + $this->num_links) < $num_pages) {
            if ($this->use_page_numbers) {
                $i = $num_pages;
            } else {
                $i = (($num_pages * $this->per_page) - $this->per_page);
            }

            $output[] = [
                'url' => $this->base_url . $this->prefix . $i . $this->suffix,
                'title' => $this->last_link,
            ];
        }

        // show all 
        if ($this->is_show_all) {
            $output[] = [
                'url' => $this->base_url . '-1',
                'title' => 'Все ' . $this->total_rows,
                'current' => $uri_page_number === -1
            ];
        }

        // Kill double slashes.  Note: Sometimes we can end up with a double slash
        // in the penultimate link so we'll kill all double slashes.
        foreach ($output as $key => $it) {
            $output[$key]['url'] = preg_replace("#([^:])//+#", "\\1/", $it['url']);
        }


        return $this->_CI->load->view($this->_CI->template_dir . 'navs/pagination', [
                    'base_url' => $this->base_url,
                    'total_rows' => $this->total_rows,
                    'per_page' => $this->per_page,
                    'is_show_all' => $this->is_show_all,
                    'is_show_limit_select' => $this->is_show_limit_select,
                    'list' => $output,
                    'pagination_limits' => $this->pagination_limits,
                    'pagination_limit' => $this->pagination_limit,
                        ], TRUE);
    }

    /**
     * get pagination limit
     * @return int
     */
    public function get_pagination_limit() {

        if (!$this->_CI instanceof MY_Controller)
            return $this->pagination_limit;

        // define pagination limit
        $user_limit = $this->_CI->get_user_settings('pagination_limit.' . str_replace('/', '_', $this->_CI->uri->uri_string()));
        $this->pagination_limit = !!$user_limit ? (int) $user_limit : $this->pagination_limit;

//        vdump($user_limit);
        
        // set new pagination limit
        if(!!($new_pl = (int)$this->_CI->input->get('pagination_limit')) && $new_pl !== $this->pagination_limit){
            $this->_set_pagination_limit($this->pagination_limit = $new_pl);
        }
        
        return $this->pagination_limit;
    }

    /**
     * Set pagination limit
     * @param int $limit
     * @return boolean
     */
    private function _set_pagination_limit($limit) {
        if (!$this->_CI instanceof MY_Controller || !(int) $limit)
            return false;

        return $this->_CI->set_user_settings('pagination_limit', str_replace('/', '_', $this->_CI->uri->uri_string()), $limit);
    }

}
