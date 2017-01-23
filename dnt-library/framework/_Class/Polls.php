<?php
class Polls{
	
	
	public function getTypes(){
		return array(
			"1" => "Anketa s percentuálnou úspešnosťou",
			"2" => "Anketa s predpokladaným výsledkom kategorizácie",
		);
	}
	
	public function currentType($type){
		foreach (self::getTypes() as $key => $value) {
			if($type == $key)
				echo "<option value='".$key."' selected>".$value."</option>"; 
			else
				echo "<option value='".$key."'>".$value."</option>"; 
		}
	}
	
	public function currentTypeStr($type){
		$types = self::getTypes();
		return $types[$type];
	}
	
	public function is_correct($poll_id){
		
	}
	
	public function getPolls(){
		return "SELECT * FROM dnt_polls WHERE `show` <> '0'";
	}
	
	public function getPollsAdmin(){
		return "SELECT * FROM dnt_polls";
	}
	
	/* 
	 *
	 *returns @integer - last id 
	 *add insert query *add insert query
	 *this function generate wrapper of current poll
	 *
	 */
	public function generatePoll(){
		$db 	= new Db;
		$rest 	= new Rest;
		$insertedData = array(
				'vendor_id' 		=> Vendor::getId(), 
				'name' 				=> $rest->post("name"), 
				'name_url' 			=> Dnt::name_url($rest->post("name")), 
				'type' 				=> $rest->post("poll_type"),
				'datetime_creat' 	=> Dnt::datetime(),
				'datetime_update' 	=> Dnt::datetime(),
				'datetime_publish' 	=> Dnt::datetime(),
				'`show`' 			=> '0',
				'count_questions' 	=> $rest->post("count_questions")
			);

		$db->insert('dnt_polls', $insertedData);
		$poll_id = $db->lastid();	
		return $poll_id;
	}
	
	/* 
	 *
	 *return var
	 *this functionreturn return dnt_polls param.
	 *
	 */
	public function getParam($column, $poll_id){
		$db 	= new Db;
		$query = "SELECT `$column` FROM dnt_polls WHERE
		vendor_id 	= ".Vendor::getId()." AND
		id 	= ".$poll_id."";
		
		if($db->num_rows($query)>0){
			foreach($db->get_results($query) as $row){
				return $row[$column];
			}
		}else{
			return false;
		}
	}
	
	/* 
	 *
	 *return $integer
	 *this functionreturn number of question in one poll.
	 *
	 */
	public function getNumberOfQuestions($poll_id){
		$db 	= new Db;
		$query = "SELECT * FROM dnt_polls_composer WHERE
		vendor_id 	= ".Vendor::getId()." AND
		poll_id 	= ".$poll_id." AND
		`key`       = 'question'";
		return $db->num_rows($query);
		
	}

	
	/* 
	 *
	 *no return 
	 *add insert query
	 *this function generate polls meta.
	 *
	 */
	public function generateDefaultComposer($poll_id){
		//get instances
		$db 				= new Db;
		$rest 				= new Rest;
		$question_id 		= 1;
		$questions 			= 5;
		$order 				= $questions;
		$winning_combination= 3;
		$count_questions	= self::getParam("count_questions", $poll_id);
		
		//generovanie inputov pre vyherne kombinacie..
		for($i=1;$i<=$winning_combination;$i++){
			$points = ($i-1)*3; //zabezpeči relevantný počet bodov
			$insertedData = array(
				'`vendor_id`' 			=> Vendor::getId(), 
				'`poll_id`' 			=> $poll_id, 
				'`question_id`' 		=> "0", // winning_combination ma vzdy index 0
				'`key`' 				=> "winning_combination",
				'`value`' 				=> "Výherná kombinácia č. $i",
				'`description`' 		=> "Výherná kombinácia č. $i",
				'`show`' 				=> "1",
				'`points`' 				=> $points,
				'`order`' 				=> "$order",
			);
			$db->insert('dnt_polls_composer', $insertedData);
		}
			
		for($j=1;$j<=$questions;$j++){
			
			//generovanie inputov pre otazky..
			$insertedData = array(
				'`vendor_id`' 			=> Vendor::getId(), 
				'`poll_id`' 			=> $poll_id, 
				'`question_id`' 		=> $question_id, 
				'`key`' 				=> "question",
				'`value`' 				=> "Otázka",
				'`description`' 		=> "Tu zadajte Vašu otázku",
				'`show`' 				=> "1",
				'`order`' 				=> "$order",
			);
			$db->insert('dnt_polls_composer', $insertedData);
				
			//generovanie inputov pre typy odpovedí A,B,C,D...
			for($i=1;$i<=$count_questions;$i++){
				$insertedData = array(
				'`vendor_id`' 			=> Vendor::getId(), 
				'`poll_id`' 			=> $poll_id, 
				'`question_id`' 		=> $question_id, 
				'`key`' 				=> "ans",
				'`value`' 				=> "Odpoveď pre otázku",
				'`description`' 		=> "Tu zadajte jednu z Vaších odpovedi pre otázku",
				'`points`' 				=> "$i",
				'`show`' 				=> "1",
				);
				$db->insert('dnt_polls_composer', $insertedData);
			}
			$question_id++;
			$order --;
		}
	}
	
	public function addQuestion($poll_id, $question_id){
		$db 				= new Db;
		$count_questions	= self::getParam("count_questions", $poll_id);
		$question_id 		= $question_id +1;
		
		$insertedData = array(
				'`vendor_id`' 			=> Vendor::getId(), 
				'`poll_id`' 			=> $poll_id, 
				'`question_id`' 		=> $question_id, 
				'`key`' 				=> "question",
				'`value`' 				=> "Otázka",
				'`description`' 		=> "Tu zadajte Vašu otázku",
				'`show`' 				=> "1",
				'`order`' 				=> "$order",
			);
		$db->insert('dnt_polls_composer', $insertedData);
		
		for($i=1;$i<=$count_questions;$i++){
			$insertedData = array(
			'`vendor_id`' 			=> Vendor::getId(), 
			'`poll_id`' 			=> $poll_id, 
			'`question_id`' 		=> $question_id, 
			'`key`' 				=> "ans",
			'`value`' 				=> "Odpoveď pre otázku",
			'`description`' 	=> "Tu zadajte jednu z Vaších odpovedi pre otázku",
			'`points`' 				=> "",
			'`show`' 				=> "1",
			);
			$db->insert('dnt_polls_composer', $insertedData);
		}
		
	}
	
	
	public function delQuestion($poll_id, $question_id){
		$db 				= new Db;
		$where = array( 'question_id' => $question_id, 'poll_id' => $poll_id, 'vendor_id' => Vendor::getId());
		$db->delete( 'dnt_polls_composer', $where);
	}
	
	
	/* 
	 *
	 *no return 
	 *this function generate polls meta via copy.
	 *
	 */
	public function copyComposer($poll_id, $copy_poll_id){
		//get instances
		$db 				= new Db;
		$query = "SELECT * FROM dnt_polls_composer WHERE
		vendor_id 	= ".Vendor::getId()." AND
		poll_id 	= ".$copy_poll_id."";
		
		if($db->num_rows($query)>0){
			foreach($db->get_results($query) as $row){
				$insertedData = array(
					'`vendor_id`' 			=> Vendor::getId(), 
					'`poll_id`' 			=> $poll_id, 
					'`question_id`' 		=> $row['question_id'], 
					'`key`' 				=> $row['key'],
					'`value`' 				=> $row['value'],
					'`description`' 		=> $row['description'],
					'`show`' 				=> $row['show'],
					'`points`' 				=> $row['points'],
					'`order`' 				=> $row['order'],
				);
				$db->insert('dnt_polls_composer', $insertedData);
			}
		}
	}
	
	/* 
	 *
	 *return @string
	 *this function returs query included 2 arguments: $poll_id and $question_id
	 *
	 */
	public function getCurrentAnsewerData($poll_id, $question_id){
		return "SELECT * FROM dnt_polls_composer WHERE
		vendor_id 	= ".Vendor::getId()." AND
		poll_id 	= ".$poll_id." AND
		`key` <> 'winning_combination' AND
		question_id = ".$question_id."";
	}
	
	
	public function getQuestions($poll_id, $question_id){
		return "SELECT * FROM dnt_polls_composer WHERE
		vendor_id 	= ".Vendor::getId()." AND
		poll_id 	= ".$poll_id." AND
		`key` LIKE '%ans%' AND
		question_id = ".$question_id."";
	}
	
	/* 
	 *
	 *return @string
	 *this function returs query included 2 arguments: $poll_id and $question_id
	 *
	 */
	public function getWinningCombinationData($poll_id){
		return "SELECT * FROM dnt_polls_composer WHERE
		vendor_id 	= ".Vendor::getId()." AND
		poll_id 	= ".$poll_id." AND
		`key` = 'winning_combination'";
	}
	
	public function getPollData($poll_id){
		return "SELECT * FROM dnt_polls_composer WHERE
		vendor_id 	= ".Vendor::getId()." AND
		poll_id 	= ".$poll_id."";
	}
	
	/* 
	 *
	 *return @string
	 *this function creat poll <input> name="{key}"
	 *
	 */
	public function inputName($prefix, $id, $column){
		return $id."_".$prefix."_". $column;
	}
	
	/* 
	 *
	 *return @string
	 *this function return integer of MAX POINTS
	 *
	 */
	public function getMaxPoint($poll_id){
		$db = new Db;
		$query = "SELECT * FROM dnt_polls_composer WHERE
		vendor_id 	= ".Vendor::getId()." AND
		`key` 	LIKE '%ans%' AND
		poll_id 	= ".$poll_id."";
		
		$points = 0;
		if($db->num_rows($query)>0){
			foreach($db->get_results($query) as $row){
				$points += $row['points'];
			}
		}else{
			$points = false;
		}
		return $points;
	}
	
}