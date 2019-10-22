<?php
//https://itsolutionstuff.com/post/codeigniter-3-restful-api-tutorialexample.html


require APPPATH . 'libraries/REST_Controller.php';

class Item extends REST_Controller {

	/**
	 * Get All Data from this method.
	 *
	 * @return Response
	 */
	public function __construct() {
		parent::__construct();
		$this->load->database();
	}

	/**
	 * Get All Data from this method.
	 *
	 * @return Response
	 */
	public function index_get($id = 0)
	{
		if(!empty($id)){
			$data = $this->db->get_where("cat_object", array('id' => $id))->row_array();
		}else{
			$data = $this->db->get("cat_object")->result();
		}
		$this->response($data, REST_Controller::HTTP_OK);
	}

	/**
	 * Get All Data from this method.
	 *
	 * @return Response
	 */
	public function index_post()
	{
		$input = $this->input->post();
        if((strlen($input)) == NULL){
            if(strlen ($input['image']) == 0){
            }else{

                $img = preg_replace('#data:image/[^;]+;base64,#', '', $input['image']);
                $image = base64_decode($img, $strict = TRUE);
                $image_name = md5(uniqid(rand(), true));// image name generating with random number with 32 characters
                $filename = $image_name . '.' . 'jpeg';
                $input["fileUpload"] = '/srv/dev/upload/' . $filename;
                file_put_contents('/srv/dev/upload/' . $filename, $image);

            }

            $input["image"] = 1;
            $this->db->insert('cat_object',$input);
            $this->response(array('Item created successfully.'), REST_Controller::HTTP_OK);
        }else{
            if(strlen ($input['image']) == 0){

            }else{
                $img = preg_replace('#data:image/[^;]+;base64,#', '', $input['image']);
                $image = base64_decode($img, $strict = TRUE);
                $image_name = md5(uniqid(rand(), true));// image name generating with random number with 32 characters
                $filename = $image_name . '.' . 'jpeg';
                $input["fileUpload"] = '/srv/dev/upload/' . $filename;
                file_put_contents('/srv/dev/upload/' . $filename, $image);
            }
//            var_dump($input);
            $this->db->insert('cat_object',$input);
            $this->response(array('Item created successfully.'), REST_Controller::HTTP_OK);
        }
	}

	/**
	 * Get All Data from this method.
	 *
	 * @return Response
	 */
	public function index_put($id)
	{



		$input = $this->put();
//		var_dump('item.php');
//        var_dump($id);
//		var_dump($input);
		$this->db->update('cat_object', $input, array('id'=>$id));

		$this->response(array('Item updated successfully.'), REST_Controller::HTTP_OK);
	}

	/**
	 * Get All Data from this method.
	 *
	 * @return Response
	 */
	public function index_delete($id)
	{
		$this->db->delete('cat_object', array('id'=>$id));

		$this->response(array('Item deleted successfully.'), REST_Controller::HTTP_OK);
	}

}
