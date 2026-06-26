<?php
// session_start();

include_once 'DbConfig.php';


class DAL extends DbConfig
{
    public function __construct()
    {
        parent::__construct();
    }
    
    
    public function redirect($url, $status) {       
        $_SESSION['status'] = $status;
		echo "<script>window.location='$url';</script>";
    }
    


    
    
    public function redirectError($url, $status) {       
        $_SESSION['error'] = $status;
		//header('Location: '.$url);
		echo "<script>window.location='$url';</script>";
    }
        
    public function logoutSessionAdmin() {       
        unset($_SESSION['loggedInAdmin']);
    }
    
    public function validation($value) {
        return $this->connection->real_escape_string($value);
    }
    public function myDate($cDate){
		$date=date_create($cDate);
		return date_format($date,"d-m-Y");
	}
    public function myDatet($cDate){
		$date=date_create($cDate);
		return date_format($date,"d-m-Y h:i:sA");
	}
    public function mytime($cDate){
		$date=date_create($cDate);
		return date_format($date,"h:i A");
	}
    public function pcDate($cDate){
		$date=date_create($cDate);
		echo date_format($date,"d-m-Y");
	}
    
    public function seo_friendly_url($string){
	    $string = str_replace(array('[\', \']'), '', $string);
	    $string = preg_replace('/\[.*\]/U', '', $string);
	    $string = preg_replace('/&(amp;)?#?[a-z0-9]+;/i', '-', $string);
	    $string = htmlentities($string, ENT_COMPAT, 'utf-8');
	    $string = preg_replace('/&([a-z])(acute|uml|circ|grave|ring|cedil|slash|tilde|caron|lig|quot|rsquo);/i', '\\1', $string );
	    $string = preg_replace(array('/[^a-z0-9]/i', '/[-]+/') , '-', $string);
	    return strtolower(trim($string, '-'));
	}
	
	public function validations($data){
		foreach($data as $key => $value ){
			$value = trim($value);
			$value = stripslashes($value);
			$value = strip_tags($value);
			$value = htmlspecialchars($value);
			
			$data[$key] = $value;
		}
		return $data;
	}
    
    public function getData($query)
    {       
        $result = $this->connection->query($query);
        if ($result == false) {
            return false;
        } 
        $rows = array();
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
        return $rows;
    }
    


    public function executeQuery($sql, $result = []) {
        $stmt = $this->connection->prepare($sql);
        if ($result) {
            $stmt->bind_param(str_repeat("s", count($result)), ...$result);
        }
        $stmt->execute();
    }


    public function getDataExits($query)
    {       
        $result = $this->connection->query($query);
        if ($result == false) {
            return false;
        } 
        return $result;
    }
        
    public function execute($query) 
    {
        $result = $this->connection->query($query);
        if ($result == false) {
            echo 'Error: cannot execute the command' . $this->connection->error;
            return false;
        } else {
            return true;
        }       
    }
    
    public function delete($id, $table) 
    { 
        $query = "DELETE FROM $table WHERE ID = $id";
        $result = $this->connection->query($query);
        if ($result == false) {
            echo 'Error: cannot delete id ' . $id . ' from table ' . $table;
            return false;
        } else {
            return true;
        }
    }
    
    public function getCount($tableName) 
    { 
        $query = "SELECT * FROM  $tableName";
        $result = $this->connection->query($query);
        if ($result) {
            $totalCount = mysqli_num_rows($result);
	 		return $totalCount;
        } else {
            return 'Something Went Wrong';
        }
    }

    // Banner Management Functions
    public function addBanner($title, $subtitle, $description, $image, $button_text, $button_link, $status, $sort_order) {
        $title = $this->validation($title);
        $subtitle = $this->validation($subtitle);
        $description = $this->validation($description);
        $image = $this->validation($image);
        $button_text = $this->validation($button_text);
        $button_link = $this->validation($button_link);
        $status = $this->validation($status);
        $sort_order = (int)$sort_order;

        $query = "INSERT INTO tbl_banners (title, subtitle, description, image, button_text, button_link, status, sort_order) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("sssssssi", $title, $subtitle, $description, $image, $button_text, $button_link, $status, $sort_order);
        
        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }

    public function updateBanner($banner_id, $title, $subtitle, $description, $image, $button_text, $button_link, $status, $sort_order) {
        $banner_id = (int)$banner_id;
        $title = $this->validation($title);
        $subtitle = $this->validation($subtitle);
        $description = $this->validation($description);
        $image = $this->validation($image);
        $button_text = $this->validation($button_text);
        $button_link = $this->validation($button_link);
        $status = $this->validation($status);
        $sort_order = (int)$sort_order;

        $query = "UPDATE tbl_banners SET title=?, subtitle=?, description=?, image=?, button_text=?, button_link=?, status=?, sort_order=? WHERE banner_id=?";
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("sssssssii", $title, $subtitle, $description, $image, $button_text, $button_link, $status, $sort_order, $banner_id);
        
        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }

    public function getBanner($banner_id) {
        $banner_id = (int)$banner_id;
        $query = "SELECT * FROM tbl_banners WHERE banner_id = $banner_id";
        return $this->getData($query);
    }

    public function getAllBanners() {
        $query = "SELECT * FROM tbl_banners ORDER BY sort_order ASC, created_at DESC";
        return $this->getData($query);
    }

    public function getActiveBanners() {
        $query = "SELECT * FROM tbl_banners WHERE status = 'active' ORDER BY sort_order ASC, created_at DESC";
        return $this->getData($query);
    }

    public function deleteBanner($banner_id) {
        $banner_id = (int)$banner_id;
        $query = "DELETE FROM tbl_banners WHERE banner_id = $banner_id";
        return $this->execute($query);
    }

    public function updateBannerStatus($banner_id, $status) {
        $banner_id = (int)$banner_id;
        $status = $this->validation($status);
        $query = "UPDATE tbl_banners SET status = '$status' WHERE banner_id = $banner_id";
        return $this->execute($query);
    }

    // Project Management Functions
    public function addProject($name, $location, $price_range, $description, $image, $status) {
        $name = $this->validation($name);
        $location = $this->validation($location);
        $price_range = $this->validation($price_range);
        $description = $this->validation($description);
        $image = $this->validation($image);
        $status = $this->validation($status);

        $query = "INSERT INTO tbl_projects (name, location, price_range, description, image, status) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("ssssss", $name, $location, $price_range, $description, $image, $status);
        
        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }

    public function updateProject($project_id, $name, $location, $price_range, $description, $image, $status) {
        $project_id = (int)$project_id;
        $name = $this->validation($name);
        $location = $this->validation($location);
        $price_range = $this->validation($price_range);
        $description = $this->validation($description);
        $status = $this->validation($status);

        if ($image) {
            $image = $this->validation($image);
            $query = "UPDATE tbl_projects SET name=?, location=?, price_range=?, description=?, image=?, status=? WHERE project_id=?";
            $stmt = $this->connection->prepare($query);
            $stmt->bind_param("ssssssi", $name, $location, $price_range, $description, $image, $status, $project_id);
        } else {
            $query = "UPDATE tbl_projects SET name=?, location=?, price_range=?, description=?, status=? WHERE project_id=?";
            $stmt = $this->connection->prepare($query);
            $stmt->bind_param("sssssi", $name, $location, $price_range, $description, $status, $project_id);
        }
        
        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }

    public function getProject($project_id) {
        $project_id = (int)$project_id;
        $query = "SELECT * FROM tbl_projects WHERE project_id = $project_id";
        return $this->getData($query);
    }

    public function getAllProjects() {
        $query = "SELECT * FROM tbl_projects ORDER BY created_at DESC";
        return $this->getData($query);
    }

    public function getActiveProjects() {
        $query = "SELECT * FROM tbl_projects WHERE status = 'active' ORDER BY created_at DESC";
        return $this->getData($query);
    }

    public function deleteProject($project_id) {
        $project_id = (int)$project_id;
        $query = "DELETE FROM tbl_projects WHERE project_id = $project_id";
        return $this->execute($query);
    }

    public function updateProjectStatus($project_id, $status) {
        $project_id = (int)$project_id;
        $status = $this->validation($status);
        $query = "UPDATE tbl_projects SET status = '$status' WHERE project_id = $project_id";
        return $this->execute($query);
    }

    // Event Banner Management Functions
    public function addEventBanner($title, $subtitle, $description, $image, $button_text, $button_link, $event_date, $end_date, $status, $sort_order) {
        $title = $this->validation($title);
        $subtitle = $this->validation($subtitle);
        $description = $this->validation($description);
        $image = $this->validation($image);
        $button_text = $this->validation($button_text);
        $button_link = $this->validation($button_link);
        $event_date = $this->validation($event_date);
        $end_date = $this->validation($end_date);
        $status = $this->validation($status);
        $sort_order = (int)$sort_order;

        $query = "INSERT INTO tbl_event_banners (title, subtitle, description, image, button_text, button_link, event_date, end_date, status, sort_order) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("sssssssssi", $title, $subtitle, $description, $image, $button_text, $button_link, $event_date, $end_date, $status, $sort_order);
        
        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }

    public function updateEventBanner($event_id, $title, $subtitle, $description, $image, $button_text, $button_link, $event_date, $end_date, $status, $sort_order) {
        $event_id = (int)$event_id;
        $title = $this->validation($title);
        $subtitle = $this->validation($subtitle);
        $description = $this->validation($description);
        $button_text = $this->validation($button_text);
        $button_link = $this->validation($button_link);
        $event_date = $this->validation($event_date);
        $end_date = $this->validation($end_date);
        $status = $this->validation($status);
        $sort_order = (int)$sort_order;

        if ($image) {
            $image = $this->validation($image);
            $query = "UPDATE tbl_event_banners SET title=?, subtitle=?, description=?, image=?, button_text=?, button_link=?, event_date=?, end_date=?, status=?, sort_order=? WHERE event_id=?";
            $stmt = $this->connection->prepare($query);
            $stmt->bind_param("sssssssssii", $title, $subtitle, $description, $image, $button_text, $button_link, $event_date, $end_date, $status, $sort_order, $event_id);
        } else {
            $query = "UPDATE tbl_event_banners SET title=?, subtitle=?, description=?, button_text=?, button_link=?, event_date=?, end_date=?, status=?, sort_order=? WHERE event_id=?";
            $stmt = $this->connection->prepare($query);
            $stmt->bind_param("ssssssssii", $title, $subtitle, $description, $button_text, $button_link, $event_date, $end_date, $status, $sort_order, $event_id);
        }
        
        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }

    public function getEventBanner($event_id) {
        $event_id = (int)$event_id;
        $query = "SELECT * FROM tbl_event_banners WHERE event_id = $event_id";
        return $this->getData($query);
    }

    public function getAllEventBanners() {
        $query = "SELECT * FROM tbl_event_banners ORDER BY sort_order ASC, created_at DESC";
        return $this->getData($query);
    }

    public function getActiveEventBanners() {
        $query = "SELECT * FROM tbl_event_banners WHERE status = 'active' ORDER BY sort_order ASC, created_at DESC";
        return $this->getData($query);
    }

    public function deleteEventBanner($event_id) {
        $event_id = (int)$event_id;
        $query = "DELETE FROM tbl_event_banners WHERE event_id = $event_id";
        return $this->execute($query);
    }

    public function updateEventBannerStatus($event_id, $status) {
        $event_id = (int)$event_id;
        $status = $this->validation($status);
        $query = "UPDATE tbl_event_banners SET status = '$status' WHERE event_id = $event_id";
        return $this->execute($query);
    }

    // Inquiry Management Functions
    public function addInquiry($data) {
        try {
            // Check if table exists
            $table_check = $this->connection->query("SHOW TABLES LIKE 'tbl_inquiries'");
            if (!$table_check || $table_check->num_rows == 0) {
                $error_msg = "Table 'tbl_inquiries' does not exist. Please run update_table_structure.sql";
                error_log("ERROR: " . $error_msg);
                throw new Exception($error_msg);
            }
            
            // Validate and sanitize only required input data from form
            $first_name = $this->validation($data['firstName']);
            $email = $this->validation($data['email']);
            $phone = $this->validation($data['phone']);
            $project_name = isset($data['projectName']) ? $this->validation($data['projectName']) : '';
            $requirements = isset($data['requirements']) ? $this->validation($data['requirements']) : '';

            $query = "INSERT INTO tbl_inquiries (
                first_name, email, phone, project_name, requirements, status
            ) VALUES (?, ?, ?, ?, ?, 'new')";
            
            $stmt = $this->connection->prepare($query);
            if (!$stmt) {
                $error_msg = "SQL Prepare Error: " . $this->connection->error;
                error_log("Inquiry - " . $error_msg);
                throw new Exception($error_msg);
            }
            
            $stmt->bind_param("sssss", 
                $first_name, $email, $phone, $project_name, $requirements
            );
            
            if ($stmt->execute()) {
                $insert_id = $this->connection->insert_id;
                error_log("Inquiry - Successfully inserted with ID: " . $insert_id);
                return $insert_id;
            } else {
                $error_msg = "SQL Execute Error: " . $stmt->error;
                error_log("Inquiry - " . $error_msg);
                throw new Exception($error_msg);
            }
        } catch (Exception $e) {
            error_log("Inquiry - Exception: " . $e->getMessage());
            throw $e;
        }
    }

    public function getInquiry($inquiry_id) {
        $inquiry_id = (int)$inquiry_id;
        $query = "SELECT * FROM tbl_inquiries WHERE id = $inquiry_id";
        return $this->getData($query);
    }

    public function getAllInquiries() {
        $query = "SELECT * FROM tbl_inquiries ORDER BY created_at DESC";
        return $this->getData($query);
    }

    public function getInquiriesByStatus($status) {
        $status = $this->validation($status);
        $query = "SELECT * FROM tbl_inquiries WHERE status = '$status' ORDER BY created_at DESC";
        return $this->getData($query);
    }

    public function updateInquiryStatus($inquiry_id, $status, $assigned_consultant = null, $notes = null) {
        $inquiry_id = (int)$inquiry_id;
        $status = $this->validation($status);
        
        // Since assigned_consultant column is dropped, only update status
        $query = "UPDATE tbl_inquiries SET status = ? WHERE id = ?";
            $stmt = $this->connection->prepare($query);
            $stmt->bind_param("si", $status, $inquiry_id);
        
        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }

    public function deleteInquiry($inquiry_id) {
        $inquiry_id = (int)$inquiry_id;
        $query = "DELETE FROM tbl_inquiries WHERE id = $inquiry_id";
        return $this->execute($query);
    }

    public function getInquiryCount() {
        $query = "SELECT COUNT(*) as total FROM tbl_inquiries";
        $result = $this->getData($query);
        return $result[0]['total'];
    }

    public function getInquiriesByDateRange($start_date, $end_date) {
        $start_date = $this->validation($start_date);
        $end_date = $this->validation($end_date);
        $query = "SELECT * FROM tbl_inquiries WHERE DATE(created_at) BETWEEN '$start_date' AND '$end_date' ORDER BY created_at DESC";
        return $this->getData($query);
    }

    // Price Request insert for modal form (tbl_price_requests)
    public function addPriceRequest($data) {
        $enquiry_id = $this->validation($data['enquiry_id'] ?? '');
        $first_name = $this->validation($data['firstName'] ?? '');
        $last_name = $this->validation($data['lastName'] ?? '');
        $email = $this->validation($data['email'] ?? '');
        $phone = $this->validation($data['phone'] ?? '');
        $alt_phone = $this->validation($data['altPhone'] ?? '');
        $occupation = $this->validation($data['occupation'] ?? '');
        $state = $this->validation($data['state'] ?? '');
        $city = $this->validation($data['city'] ?? '');
        $area = $this->validation($data['area'] ?? '');
        $pincode = $this->validation($data['pincode'] ?? '');
        $property_type = isset($data['propertyType']) ? (is_array($data['propertyType']) ? implode(', ', $data['propertyType']) : $this->validation($data['propertyType'])) : '';
        $bhk = $this->validation($data['bhk'] ?? '');
        $budget = $this->validation($data['budget'] ?? '');
        $purpose = $this->validation($data['purpose'] ?? '');
        $timeline = $this->validation($data['timeline'] ?? '');
        $requirements = $this->validation($data['requirements'] ?? '');
        $loan_required = $this->validation($data['loanRequired'] ?? '');
        $referral = $this->validation($data['referral'] ?? '');
        $newsletter = isset($data['newsletter']) ? (int)$data['newsletter'] : 0;
        $whatsapp = isset($data['whatsapp']) ? (int)$data['whatsapp'] : 0;
        $site_visit = isset($data['siteVisit']) ? (int)$data['siteVisit'] : 0;
        $project_id = isset($data['project_id']) ? (int)$data['project_id'] : null;

        $query = "INSERT INTO tbl_price_requests (
            enquiry_id, first_name, last_name, email, phone, alt_phone, occupation,
            state, city, area, pincode, property_type, bhk, budget, purpose, timeline,
            requirements, loan_required, referral, newsletter, whatsapp, site_visit, status, project_id
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'new', ?)`";

        // Correct the query (remove stray backtick if any) by preparing properly
        $query = "INSERT INTO tbl_price_requests (
            enquiry_id, first_name, last_name, email, phone, alt_phone, occupation,
            state, city, area, pincode, property_type, bhk, budget, purpose, timeline,
            requirements, loan_required, referral, newsletter, whatsapp, site_visit, status, project_id
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'new', ?)";

        $stmt = $this->connection->prepare($query);
        $values = [
            $enquiry_id, $first_name, $last_name, $email, $phone, $alt_phone, $occupation,
            $state, $city, $area, $pincode, $property_type, $bhk, $budget, $purpose, $timeline,
            $requirements, $loan_required, $referral, $newsletter, $whatsapp, $site_visit, $project_id
        ];
        // Types: 19 strings + 4 integers
        $types = str_repeat('s', 19) . 'iiii';
        $stmt->bind_param($types, ...$values);

        if ($stmt->execute()) {
            return $this->connection->insert_id;
        }
        return false;
    }

    // Simple Price Request insert (for simple form with name, email, phone, project_name)
    public function addSimplePriceRequest($data) {
        try {
            // Check if table exists
            $table_check = $this->connection->query("SHOW TABLES LIKE 'tbl_price_requests'");
            if (!$table_check || $table_check->num_rows == 0) {
                $error_msg = "Table 'tbl_price_requests' does not exist. Please run create_price_requests_table.sql or update_price_requests_table.sql";
                error_log("ERROR: " . $error_msg);
                throw new Exception($error_msg);
            }
            
            $enquiry_id = isset($data['enquiry_id']) ? $this->validation($data['enquiry_id']) : '';
            $first_name = $this->validation($data['firstName'] ?? '');
            $email = $this->validation($data['email'] ?? '');
            $phone = $this->validation($data['phone'] ?? '');
            $project_name = isset($data['projectName']) ? $this->validation($data['projectName']) : '';
            
            // Generate enquiry ID if not provided
            if (empty($enquiry_id)) {
                $enquiry_id = 'PRICE-' . date('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
            }

            // Debug: Log the project name to ensure it's being received
            error_log("Price Request - Project Name: " . $project_name);
            error_log("Price Request - Data received: " . print_r($data, true));

            // Insert only the fields that are being sent from the form
            $query = "INSERT INTO tbl_price_requests (
                enquiry_id, first_name, email, phone, project_name, status
            ) VALUES (?, ?, ?, ?, ?, 'new')";
            
            $stmt = $this->connection->prepare($query);
            if (!$stmt) {
                $error_msg = "SQL Prepare Error: " . $this->connection->error;
                error_log("Price Request - " . $error_msg);
                throw new Exception($error_msg);
            }
            
            $stmt->bind_param("sssss", 
                $enquiry_id, $first_name, $email, $phone, $project_name
            );
            
            if ($stmt->execute()) {
                $insert_id = $this->connection->insert_id;
                error_log("Price Request - Successfully inserted with ID: " . $insert_id);
                return $insert_id;
            } else {
                $error_msg = "SQL Execute Error: " . $stmt->error;
                error_log("Price Request - " . $error_msg);
                throw new Exception($error_msg);
            }
        } catch (Exception $e) {
            error_log("Price Request - Exception: " . $e->getMessage());
            throw $e;
        }
    }

    // Add Project Details Request
    public function addProjectDetailsRequest($data) {
        try {
            // Check if table exists
            $table_check = $this->connection->query("SHOW TABLES LIKE 'tbl_project_details_requests'");
            if (!$table_check || $table_check->num_rows == 0) {
                $error_msg = "Table 'tbl_project_details_requests' does not exist. Please run create_project_details_requests_table.sql";
                error_log("ERROR: " . $error_msg);
                throw new Exception($error_msg);
            }
            
            $first_name = $this->validation($data['firstName'] ?? '');
            $email = $this->validation($data['email'] ?? '');
            $phone = $this->validation($data['phone'] ?? '');
            $address = isset($data['address']) ? $this->validation($data['address']) : '';
            $requirement = isset($data['requirement']) ? $this->validation($data['requirement']) : '';
            
            $query = "INSERT INTO tbl_project_details_requests (
                first_name, email, phone, address, requirement, status
            ) VALUES (?, ?, ?, ?, ?, 'new')";
            
            $stmt = $this->connection->prepare($query);
            if (!$stmt) {
                $error_msg = "SQL Prepare Error: " . $this->connection->error;
                error_log("Project Details Request - " . $error_msg);
                throw new Exception($error_msg);
            }
            
            $stmt->bind_param("sssss", 
                $first_name, $email, $phone, $address, $requirement
            );
            
            if ($stmt->execute()) {
                $insert_id = $this->connection->insert_id;
                error_log("Project Details Request - Successfully inserted with ID: " . $insert_id);
                return $insert_id;
            } else {
                $error_msg = "SQL Execute Error: " . $stmt->error;
                error_log("Project Details Request - " . $error_msg);
                throw new Exception($error_msg);
            }
        } catch (Exception $e) {
            error_log("Project Details Request - Exception: " . $e->getMessage());
            throw $e;
        }
    }
    
    // Get all Project Details Requests
    public function getAllProjectDetailsRequests() {
        $query = "SELECT * FROM tbl_project_details_requests ORDER BY created_at DESC";
        return $this->getData($query);
    }
    
    // Get Project Details Request by ID
    public function getProjectDetailsRequest($id) {
        $id = (int)$id;
        $query = "SELECT * FROM tbl_project_details_requests WHERE id = $id";
        return $this->getData($query);
    }
    
    // Update Project Details Request Status
    public function updateProjectDetailsRequestStatus($id, $status) {
        $id = (int)$id;
        $status = $this->validation($status);
        
        $query = "UPDATE tbl_project_details_requests SET status = ? WHERE id = ?";
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("si", $status, $id);
        
        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }
    
    // Delete Project Details Request
    public function deleteProjectDetailsRequest($id) {
        $id = (int)$id;
        $query = "DELETE FROM tbl_project_details_requests WHERE id = $id";
        return $this->execute($query);
    }

    // Generic update method
    public function updateData($table, $data, $where) {
        $set_clause = [];
        foreach ($data as $column => $value) {
            $value = $this->validation($value);
            $set_clause[] = "$column = '$value'";
        }
        $set_string = implode(', ', $set_clause);
        $query = "UPDATE $table SET $set_string WHERE $where";
        return $this->execute($query);
    }

    // Generic delete method
    public function deleteData($table, $where) {
        $query = "DELETE FROM $table WHERE $where";
        return $this->execute($query);
    }

}



?>