<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Syllabus extends CI_Controller
{
	private $language;
	function __construct()
	{
		parent:: __construct();
		$this->general->session_check();
		$this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
		$this->output->set_header('Pragma: no-cache');
		$this->language = $this->crud_model->getLanguage();
	}

	public function index()
	{
		$data['title']     = 'Syallabus :: BrainPad Wave';
		$data['page']      = 'admin/page/syllabus/index';
		$data['rec']       = $this->db
                            ->distinct('example.ex_id')
                            ->join('standard','standard.board_id=board.bd_id','left')
							->join('subject','subject.std_id=standard.std_id','left')
                            ->join('chapter','chapter.std_id=standard.std_id','left')
                            ->join('topics','topics.ch_id=chapter.ch_id','left')
                            ->join('subtopics','subtopics.tp_id=topics.tp_id','left')
                            ->join('example','example.stp_id=subtopics.stp_id','left')
                            ->join('category','category.c_id=example.cat_id','left')
                            ->join('layout','layout.cat_id=category.c_id','left')
                            ->where('board.bd_name',$this->session->userdata('board_name'))
                            ->order_by("example.ex_id","asc")
                            ->group_by('example.ex_id')
                            ->get('board')
                            ->result_array();
        
		$this->load->view('admin/partials/layout',$data);
	}

	public function index_api(){
		$board_id 	  = $this->input->post('board_id');
		$std_id 	  = $this->input->post('std_id');
		$subject_id   = $this->input->post('sub_id');
		$chapter_id   = $this->input->post('chapter_id');
		$topic_id 	  = $this->input->post('topic_id');
		$subtopic_id  = $this->input->post('subtopic_id');

		$syllabus = array( 
			'board_id'=>$board_id, 	  
			'std_id'=>$std_id, 	  
			'subject_id'=>$subject_id,   
			'chapter_id'=>$chapter_id,   
			'topic_id'=>$topic_id,
		    'subtopic_id'=>$subtopic_id,
		 );
		 $this->session->set_userdata('syllabus', $syllabus);

		$query =	 $this->db
				->distinct('example.ex_id')
				->join('standard','standard.board_id=board.bd_id','left')
				->join('subject','subject.std_id=standard.std_id','left')
				->join('chapter','chapter.std_id=standard.std_id','left')
				->join('topics','topics.ch_id=chapter.ch_id','left')
				->join('subtopics','subtopics.tp_id=topics.tp_id','left')
				->join('example','example.stp_id=subtopics.stp_id','left')
				->join('category','category.c_id=example.cat_id','left')
				->join('layout','layout.lay_id=example.layout_id','left')
				->join('animation','animation.anim_id=example.animation_id','left')
				->where('board.bd_name',$this->session->userdata('board_name'))
				->where('example.stp_id',$subtopic_id)
				->order_by("example.sequence","asc")
				->group_by('example.ex_id');
		$query = $query->get('board');
				
		
		$data = [];
		$all_data = [];
		foreach($query->result() as $r) { 
			$data['stp_id']    = $r->stp_id;
			$data['std_name']  = $r->std_name;
			$data['sub_name']= $r->sub_name;
			$data['chapter_text']  =	$r->chapter_text;
			$data['topic_text']    =	$r->topic_text;
			$data['subtopic_text'] =	$r->subtopic_text;
			$data['c_name'] = $r->c_name;
			$data['lay_name'] = $r->lay_name;
			$data['ex_id'] = $r->ex_id;
			$data['ex_heading'] = $r->ex_heading;
			$data['sequence'] = $r->sequence;
			$data['ex_status'] = $r->ex_status;
			$data['bd_name'] = $this->session->userdata('board_name');
			$data['anim_name'] = $r->anim_name;
			$data['anim_description'] = $r->anim_description;
			$data['lay_name'] = $r->lay_name;
			$data['lay_description'] = $r->lay_description;
			$data['ex_title'] = $r->ex_title;
			$data['ex_heading'] = $r->ex_heading;
			$data['ex_audio'] = $r->ex_audio;
			
			$all_data[] = $data;
		} 

		$result = array(
			"data" => $all_data
		);
		$data['tabledata'] = $this->load->view('admin/page/syllabus/table',$result, TRUE);
		$data['no'] = $query->num_rows();
		// echo "<pre>"; print_r($data); exit;
		echo json_encode($data);
	}
}
