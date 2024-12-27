<?php
# CLI is required to run this code check
if (php_sapi_name() !== 'cli') {
    die("This script must be run from the command line.\n");
}

# Task Parameters
class task {
	public int $id;
	public $description;
	public $status = array("todo", "in-progress", "done");
	public $createdAt;
	public $updatedAt;

	function __construct($id, $description) {
		$this->id = $id;
		$this->description = $description;
	}
}

try { 
	# Open file
	$file = 'tasks.json';

	# File extraction
	$jsonExtract = file_get_contents($file);
	$jsonFile = json_decode($jsonExtract, true);

	# Input read and actions
	switch($argv[1]) {
		case "add":
			if (!file_exists($file) || filesize($file) === 0) {
				file_put_contents($file, "[]");
			}
			
			$idCounter = !empty($jsonFile) ? end($jsonFile)['id'] + 1 : 1;

			$task = new task($idCounter, $argv[2]);
			$task->createdAt = date("Y-m-d H:i:s");
			$task->updatedAt = $task->createdAt;
			$task->status = $task->status[0];

			# Object conversion to appropariate JSON
			$jsonFile[] = $task;
			file_put_contents($file, json_encode($jsonFile, JSON_PRETTY_PRINT));

			break;

		case "update":
			$taskID = $argv[2];
			$newDescription = $argv[3];

			foreach($jsonFile as &$task) {
				if ($taskID == $task['id']) {
					$task['description'] = $newDescription;
					$task['updatedAt'] = date('Y-m-d H:i:s');
					file_put_contents($file, json_encode($jsonFile, JSON_PRETTY_PRINT));
					break;
				}
			}

			break;

		case "delete":
			$taskID = $argv[2];

			foreach($jsonFile as $key=>$task) {
				if ($taskID == $task['id']) {
					unset($jsonFile[$key]);
					
					# ID Re-indexation
					$counter = 1;
					foreach($jsonFile as &$task) {
						$task['id'] = $counter;
						$counter++;
					}
					file_put_contents($file, json_encode($jsonFile, JSON_PRETTY_PRINT));
				}
			}

			break;

		case "mark-in-progress":
			$taskID = $argv[2];

			foreach($jsonFile as &$task) {
				if ($taskID == $task['id']) {
					$task['status'] = "in-progress";
					$task['updatedAt'] = date('Y-m-d H:i:s');
					file_put_contents($file, json_encode($jsonFile, JSON_PRETTY_PRINT));
					break;
				}
			}

			break;

		case "mark-done":
			$taskID = $argv[2];

			foreach($jsonFile as &$task) {
				if ($taskID == $task['id']) {
					$task['status'] = "done";
					$task['updatedAt'] = date('Y-m-d H:i:s');
					file_put_contents($file, json_encode($jsonFile, JSON_PRETTY_PRINT));
					break;
				}
			}

			break;

		case "list":
			if($argc == 2) {
				echo "All tasks" . PHP_EOL;
					foreach($jsonFile as $task) {
						echo $task['id'] . ") " . $task['description'] . " " . $task['status'] . PHP_EOL;
					}
			} else {
			switch($argv[2]) {
				case "done":
					echo "Tasks that are done:";
					foreach($jsonFile as $task) {
						if($task['status'] == 'done') {
							echo $task['id'] . ") " . $task['description'] . PHP_EOL;
						}
					}
					break;

				case "todo":
					echo "Tasks to do:";
					foreach($jsonFile as $task) {
						if($task['status'] == 'todo') {
							echo $task['id'] . ") " . $task['description'] . PHP_EOL;
						}
					}
					break;

				case "in-progress":
					case "todo":
						echo "Tasks to do:";
						foreach($jsonFile as $task) {
							if($task['status'] == 'in-progress') {
								echo $task['id'] . ") " . $task['description'] . PHP_EOL;
							}
						}
					break;
			}
	}
	}
} catch (Exception $e) {
	echo "Something is wrong...";
}


?>