<?php
defined('BASEPATH') OR exit('No direct script access allowed');
 
/**
 * User class.
 * 
 * @extends CI_Controller
 */
class User extends CI_Controller {

    private $error;
    private $success;
	
	private function handle_error($err) 
	{
    $this->error .= $err . "\r\n";
    }

    private function handle_success($succ) {
    $this->success .= $succ . "\r\n";
    }

	/**
	 * __construct function.
	 * 
	 * @access public
	 * @return void
	 */
	public function __construct() {
		
		parent::__construct();
		$this->load->library(array('session'));
		$this->_REQ = $_POST + $_GET;
		$this->load->helper(array('form','url'));
		$this->load->model('user_model');
		$this->load->model('file_model', 'file');
		}
	
	
	
	/*function view($page='home'){
  $this->load->view("home");
    }
	*/
	
	public function index() {
		

		
	}
	
	/**
	 * register function.
	 * 
	 * @access public
	 * @return void
	 */ 
	public function register() {
		
		// create the data object
		$data = new stdClass();
		
		// load form helper and validation library
		$this->load->helper('form');
		$this->load->library('form_validation');
		
		// set validation rules
		$this->form_validation->set_rules('username', 'Username', 'trim|required|alpha_numeric|min_length[4]|is_unique[users.username]', array('is_unique' => 'This username already exists. Please choose another one.'));
		$this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email|is_unique[users.email]');
		$this->form_validation->set_rules('password', 'Password', 'trim|required|min_length[6]');
		$this->form_validation->set_rules('password_confirm', 'Confirm Password', 'trim|required|min_length[6]|matches[password]');
		
		if ($this->form_validation->run() === false) {
			
			// validation not ok, send validation errors to the view
			$this->load->view('header');
			$this->load->view('register', $data);
			$this->load->view('footer');
			
		} else {
			
			// set variables from the form
			$username = $this->input->post('username');
			$email    = $this->input->post('email');
			$password = $this->input->post('password');
			
			if ($this->user_model->create_user($username, $email, $password)) {
				
				// user creation ok
				$this->load->view('header');
				$this->load->view('register_success', $data);
				$this->load->view('footer');
				
			} else {
				
				// user creation failed, this should never happen
				$data->error = 'There was a problem creating your new account. Please try again.';
				
				// send error to the view
				$this->load->view('header');
				$this->load->view('register', $data);
				$this->load->view('footer');
				
			}
			
		}
		
	}
		
	/**
	 * login function.
	 * 
	 * @access public
	 * @return void
	 */
	public function login() {
		
		// create the data object
		$data = new stdClass();
		
		// load form helper and validation library
		$this->load->helper('form');
		$this->load->library('form_validation');
		
		// set validation rules
		$this->form_validation->set_rules('username', 'Username', 'required|alpha_numeric');
		$this->form_validation->set_rules('password', 'Password', 'required');
		
		if ($this->form_validation->run() == false) {
			
			// validation not ok, send validation errors to the view
			//$this->load->view('header');
			$this->load->view('user/user_login');
			//$this->load->view('footer');
			
		} else {
			
			// set variables from the form
			$username = $this->input->post('username');
			$password = $this->input->post('password');
			
			if ($this->user_model->resolve_user_login($username, $password)) {
				
				$user_id = $this->user_model->get_user_id_from_username($username);
				$user    = $this->user_model->get_user($user_id);
				
				// set session user datas
				$_SESSION['user_id']      = (int)$user->id;
				$_SESSION['username']     = (string)$user->username;
				$_SESSION['logged_in']    = (bool)true;
				//$_SESSION['is_confirmed'] = (bool)$user->is_confirmed;
				//$_SESSION['is_admin']     = (bool)$user->is_admin;
				
				redirect('UserDashboard');
				
				$this->session->set_userdata($data);
				
				// user login ok
				//$this->load->view('header');
				//$this->load->view('user/login_success', $data);
				//$this->load->view('footer');
				
				
				
			} else {
				// login failed
				$data->error = 'Wrong username or password.';
				// send error to the view
				//$this->load->view('header');
				$this->load->view('user/user_login', $data);
				//$this->load->view('footer');
			}
		}
		
	}
	
		public function dashboard() {
		//$this->load->view("user/user-dashboard");
	    if ($this->session->userdata('logged_in')) {
        $username = $this->session->userdata('username'); 
        //Get your db results
    	 //  $this->load->user('user_model');
        $data['results']=$this->user_model->getOne($username);
        $this->load->view('user/user-dashboard',$data);
		
    } else{
    //What you want to happen when they are not logged in. 
 	redirect("UserLogin");
    }
	}	
	
	
	public function ourprofile() {
		//$this->load->view("user/user-dashboard");
	    if ($this->session->userdata('logged_in')) {
        $username = $this->session->userdata('username'); 
        //Get your db results
    	 //  $this->load->user('user_model');
        $data['results']=$this->user_model->getOne($username);
        $this->load->view('user/our-profile',$data);
		
    } else{
    //What you want to happen when they are not logged in. 
 	redirect("UserLogin");
    }
	}	
	
	
	
 function show_user(){
 	   $id = $this->session->user_id;
	   $data['single_user'] = $this->user_model->show_user_id($id);
	   //echo $url_segment2=$this->uri->segment(2);
	   $this->load->view('user/user-data', $data);
		
}


 function show_user_details(){
 	   $id = $this->session->user_id;
	   $data['userdetails'] = $this->user_model->show_user_id($id);
	   //echo $url_segment2=$this->uri->segment(2);
	   $this->load->view('user/edit-profile', $data);
		
}
 

function update_user_id1() {
 if($this->input->post('userSubmit')){
if(!empty($_FILES['picture']['name'])){
                $config['upload_path'] = 'uploads';
                $config['allowed_types'] = 'jpg|jpeg|png|gif';
                $config['file_name'] = $_FILES['picture']['name'];
                
                //Load upload library and initialize configuration
                $this->load->library('upload',$config);
                $this->upload->initialize($config);
                
                if($this->upload->do_upload('picture')){
                    $uploadData = $this->upload->data();
                    $picture = $uploadData['file_name'];
                }else{
                   // $picture = '';
                }
            }else{
               // $picture = '';
            }
       $id= $this->input->post('id');
       $data = array(
       'username' => $this->input->post('username'),
       'email' => $this->input->post('email'),
	   'aboutus' => $this->input->post('aboutus'),
	    'picture' => $picture
        );
       $this->user_model->update_user_id1($id,$data);
	   
	   
      // $this->show_user_id();
	// redirect('register');
	}
}



function updateUserDetails() {
 if($this->input->post('userUpdate')){
 
       $id= $this->input->post('id');
       $data = array(
      			 'username' => $this->input->post('username'),
       			 'email' => $this->input->post('email'),
	   			 'gender' => $this->input->post('gender'),
	   	   		 'speciality' => $this->input->post('speciality'),
		   	     'experience' => $this->input->post('experience'),
			     'qualification' => $this->input->post('qualification'),
				 'mcrno' => $this->input->post('mcrno'),
 				 'description' => $this->input->post('description'),
				 'awards' => $this->input->post('awards'),
        );
       if($this->user_model->updateUserDetails($id,$data)){
	    redirect('register');
	   }
	   else{
	echo "<script>
alert('Successfully Updated User Details');
location.href='UserEditProfile';
</script>";
	   }
        // $this->show_user_id();
		// redirect('register');
	  }
}

function updateContactDetails() {
 if($this->input->post('updateContact')){
 
       $id= $this->input->post('id');
       $data = array(
				 'phoneno' => $this->input->post('phoneno'),
   		   	     'landlineno' => $this->input->post('landlineno'),
				 'address' => $this->input->post('address'),
				 'gmapcode' => $this->input->post('gmapcode'),
				 'fb_link' => $this->input->post('fb_link'),
				 'twitter_link' => $this->input->post('twitter_link'),
				 'linkdin_link' => $this->input->post('linkdin_link'),
        );
       if($this->user_model->updateContactDetails($id,$data)){
	    redirect('register');
	   }
	   else{
	echo "<script>alert('Successfully Updated Contact Details');location.href='UserEditProfile';</script>";
	   }
        // $this->show_user_id();
		// redirect('register');
	  }
}


function updateUProfile() {
 if($this->input->post('updateUProfile')){
 
       $id= $this->input->post('id');
       $data = array('aboutus' => $this->input->post('aboutus'));
       $this->user_model->updateUProfile($id,$data);
	   // $this->show_user_id();
	  // redirect('register');
}
}



function updateProfilePic() {
$this->load->library('form_validation');
if (empty($_FILES['picture']['name']))
{
    $this->form_validation->set_rules('picture', 'Document', 'required');
}

 if($this->input->post('userPicture')){
if(!empty($_FILES['picture']['name'])){
                $config['upload_path'] = 'uploads';
                $config['allowed_types'] = 'jpg|jpeg|png|gif';
                $config['file_name'] = $_FILES['picture']['name'];
                
                //Load upload library and initialize configuration
                $this->load->library('upload',$config);
                $this->upload->initialize($config);
                
                if($this->upload->do_upload('picture')){
                    $uploadData = $this->upload->data();
                    $picture = $uploadData['file_name'];
                }else{
                   // $picture = '';
                }
            }else{
               // $picture = '';
            }
       $id= $this->input->post('id');
       $data = array(
	    'picture' => $picture
        );
       if($this->user_model->updateProfilePic($id,$data)){
	   	echo "<script>
location.href='UserEditProfile';
</script>";
	   }else{
	   
	   	echo "<script>
alert('Successfully Upadated Profile Pic');
location.href='UserEditProfile';
</script>";
	   }
	   
	   
	   
     // $this->show_user_id();
	// redirect('register');
	}
}




function updateClinicLogo() {
 if($this->input->post('userLogo')){
if(!empty($_FILES['clinic_logo']['name'])){
                $config['upload_path'] = 'cliniclogo';
                $config['allowed_types'] = 'jpg|jpeg|png|gif';
                $config['file_name'] = $_FILES['clinic_logo']['name'];
               //Load upload library and initialize configuration
                $this->load->library('upload',$config);
                $this->upload->initialize($config);
                if($this->upload->do_upload('clinic_logo')){
                $uploadData = $this->upload->data();
                $clinic_logo = $uploadData['file_name'];
                }else{
                   // $picture = '';
                }
                }else{
               // $picture = '';
            }
       $id= $this->input->post('id');
       $data = array(
	    'clinic_logo' => $clinic_logo
        );
       if($this->user_model->updateClinicLogo($id,$data)){
	   	   	echo "<script>
 location.href='UserEditProfile';
</script>";
	   }else{
	   	   	echo "<script>
alert('Successfully Updated Logo');
location.href='UserEditProfile';
</script>";
	   }
	   
	   
      // $this->show_user_id();
	// redirect('register');
	}
}


 
 
 
 function ChangePassword() {
 if($this->input->post('userPassword')){
 $data = new stdClass();
	   $this->load->helper('form');
	   $this->load->library('form_validation');
	   $this->form_validation->set_rules('password', 'Password', 'trim|required|min_length[6]');
		$this->form_validation->set_rules('password_confirm', 'Confirm Password', 'trim|required|min_length[6]|matches[password]');
		
		if ($this->form_validation->run() === false) {
			
			// validation not ok, send validation errors to the view
			//$this->load->view('header');
			//$this->load->view('admin/add-user', $data);
			//$this->load->view('footer');
			echo "<script>alert('Error');location.href='UserEditProfile';</script>";
			
		} else {
		
		     $id= $this->input->post('id');
	   $password=$this->input->post('password');
       //$data = array('password' =>  $this->hash_password($password));
       //$this->user_model->updateUserPassword($id,$data);
	   // $this->show_user_id();
	  // redirect('register');
	  
	  if($this->user_model->updateUserPassword($id,$password)){
	    redirect('register');
	   }
	   else{
	echo "<script>alert('Successfully Updated Contact Details');location.href='UserEditProfile';</script>";
	   }
		
		}
		
  
}
}

	 
	/**
	 * logout function.
	 * 
	 * @access public
	 * @return void
	 */
	public function logout() {
		
		// create the data object
	//	$data = new stdClass();
		
		if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
			
			// remove session datas
			foreach ($_SESSION as $key => $value) {
				unset($_SESSION[$key]);
			}
			
			// user logout ok
			//$this->load->view('header');
			//$this->load->view('user/logout_success', $data);
			//$this->load->view('footer');
			redirect('UserLogin');
		} else {
			
			// there user was not logged in, we cannot logged him out,
			// redirect him to site root
			redirect('/');
			
		}
		
	}
	
	
	
	public function users()  
      {  
         //load the database  
         //$this->load->database();  
         //load the model  
   
         //load the method of model  
         $data['h']=$this->user_model->select();  
         //return the data in view 
		 $this->load->view('header');
         $this->load->view('user/users', $data);
		 $this->load->view('footer');  
      }  
	  
	  
	  
	      function fileUploads() {
		  
        if ($this->input->post('file_upload')) {
            //file upload destination
            $dir_path = 'fileuploads';
            $config['upload_path'] = $dir_path;
            $config['allowed_types'] = '*';
            $config['max_size'] = '0';
            $config['max_filename'] = '255';
            $config['encrypt_name'] = TRUE;

            //upload file
            $i = 0;
            $files = array();
            $is_file_error = FALSE;

            if ($_FILES['upload_file1']['size'] <= 0) {
                $this->handle_error('Select at least one file.');
            } else {
                foreach ($_FILES as $key => $value) {
                    if (!empty($value['name'])) {
                        $this->load->library('upload', $config);
                        if (!$this->upload->do_upload($key)) {
                            $this->handle_error($this->upload->display_errors());
                            $is_file_error = TRUE;
                        } else {
                            $files[$i] = $this->upload->data();
                            ++$i;
                        }
                    }
                }
            }

            // There were errors, we have to delete the uploaded files
            if ($is_file_error && $files) {
                for ($i = 0; $i < count($files); $i++) {
                    $file = $dir_path . $files[$i]['file_name'];
                    if (file_exists($file)) {
                        unlink($file);
                    }
                }
            }

            if (!$is_file_error && $files) {
                $resp = $this->file->save_files_info($files);
                if ($resp === TRUE) {
                    $this->handle_success('File(s) was/were successfully uploaded.');
                } else {
                    for ($i = 0; $i < count($files); $i++) {
                        $file = $dir_path . $files[$i]['file_name'];
                        if (file_exists($file)) {
                            unlink($file);
                        }
                    }
                    $this->handle_error('Error while saving file info to Database.');
                }
            }
        }
	  //  $id= $this->input->post('id');
        $data['errors'] = $this->error;
        $data['success'] = $this->success;
		//$data['gallery']= $this->user_model->galOne($id);
		 	   $id = $this->session->user_id;
	   $data['galdetails'] = $this->user_model->show_user_id($id);
        $this->load->view('user/gallery', $data);
		
		        
        //Get your db results
    	 //  $this->load->user('user_model');
        
 
    }
	
}
