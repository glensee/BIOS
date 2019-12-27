<?php
require_once 'common.php';
require_once 'validations.php';

function error($file, $line, $messages)
{
	return ["file" => $file, "line" => $line, "message" => $messages];
}
function strip_data($data)
{
	$results = [];

	foreach ($data as $d) {
		$results[] = trim($d);
	}

	return $results;
}

function common_validation($data, $header, $line, $csv_name)
{
	$messages = [];
	// $skip = ['day', 'size', 'edollar', 'amount'];
	for ($i = 0; $i < count($data); $i++) {
		if (empty($data[$i]) && $data[$i] !== 0 && $data[$i] !== '0') {
			$messages[] = "blank " . $header[$i]; # Add error message to $messages
		}
	}
	$errors = ["file" => $csv_name, "line" => $line, "message" => $messages]; # Dictionary of errors
	return $errors;
}

function checktime($time)
{
	$split = explode(':', $time);
	$valid = FALSE;
	if (count($split) == 2) {
		$min = $split[1];
		$hr = $split[0];
		if (strlen($min) <= 2 && strlen($hr) <= 2 && 0 <= $min && $min <= 59 && 0 <= $hr && $hr <= 23) {
			$valid = TRUE;
		}
	}
	return $valid;
}

function doBootstrap()
{
	$errors = array();
	# need tmp_name -a temporary name create for the file and stored inside apache temporary folder- for proper read address
	$zip_file = $_FILES["bootstrap-file"]["tmp_name"];

	# Directory path used for temporary files
	$temp_dir = sys_get_temp_dir();
	# For macOS: Create temp folder to store extracted csvs in /app/temp/ directory
	if ((strpos(php_uname(), "Darwin") !== FALSE)) {
		$temp_dir = "temp";
		if (!is_dir($temp_dir)) {
			mkdir($temp_dir, 0777, true);
		}
	}
	# Get temp dir on system for uploading

	# keep track of number of lines successfully processed for each file
	$bid_processed = 0;
	$course_processed = 0;
	$course_completed_processed = 0;
	$prerequisite_processed = 0;
	$section_processed = 0;
	$student_processed = 0;

	# check file size
	if ($_FILES["bootstrap-file"]["size"] <= 0)
		$errors[] = "input files not found";

	else {

		$zip = new ZipArchive;
		$res = $zip->open($zip_file);

		if ($res === TRUE) {
			$zip->extractTo($temp_dir);
			$zip->close();

			$bid_path = "$temp_dir/bid.csv";
			$course_path = "$temp_dir/course.csv";
			$course_completed_path = "$temp_dir/course_completed.csv";
			$prerequisite_path = "$temp_dir/prerequisite.csv";
			$section_path = "$temp_dir/section.csv";
			$student_path = "$temp_dir/student.csv";

			$bid = @fopen($bid_path, "r");
			$course = @fopen($course_path, "r");
			$course_completed = @fopen($course_completed_path, "r");
			$prerequisite = @fopen($prerequisite_path, "r");
			$section = @fopen($section_path, "r");
			$student = @fopen($student_path, "r");

			$connMgr = new ConnectionManager();
			$conn = $connMgr->getConnection();

			# start processing

			# truncate current SQL tables
			$bidDAO = new BidDAO();
			$bidDAO->removeAll();

			$courseDAO = new CourseDAO();
			$courseDAO->removeAll();

			$course_completed_DAO = new CourseCompletedDAO();
			$course_completed_DAO->removeAll();

			$prerequisiteDAO = new PrerequisiteDAO();
			$prerequisiteDAO->removeAll();

			$sectionDAO = new SectionDAO();
			$sectionDAO->removeAll();

			$studentDAO = new StudentDAO();
			$studentDAO->removeAll();

			$roundDAO = new RoundDAO();
			$roundDAO->removeAll();

			$section_student_DAO = new SectionStudentDAO();
			$section_student_DAO->removeAll();

			$roundDAO->add(1, 'started');

			# then read each csv file line by line (remember to skip the header)
			# $data = fgetcsv($file) gets you the next line of the CSV file which will be stored
			# in the array $data
			# $data[0] is the first element in the csv row, $data[1] is the 2nd, ....

			# process each line and check for errors

			// -------------------------------------------------------------------------------------
			// Student

			// process each line, check for errors, then insert if no errors
			$header = fgetcsv($student);
			$line = 2;
			while (($data = fgetcsv($student)) !== false) {
				$messages = [];
				# strip whitespaces if any
				$data = strip_data($data);
				# Checks if any of the fields are blank
				$common_errors = common_validation($data, $header, $line, "student.csv");
				# If there are blank fields, they are added into $errors and continues on to the next row
				if (!isEmpty($common_errors["message"])) {
					$errors[] = $common_errors;
				} else {
					# Checks if userid is valid
					if (strlen($data[0]) > 128) {
						$messages[] = "invalid userid";
					}
					# Checks if userid already exists
					if ($studentDAO->retrieve($data[0])) {
						$messages[] = "duplicate userid";
					}
					# Checks if e-dollar is a positive numeric value and has less than 2 decimal places
					if (!isNonNegativeFloat($data[4]) || preg_match('/\.\d{3,}/', $data[4])) {
						$messages[] = "invalid e-dollar";
					}
					# Checks if password exceeds 128 chars
					if (strlen($data[1]) > 128) {
						$messages[] = "invalid password";
					}
					# Checks if name exceeds 100 chars
					if (strlen($data[2]) > 100) {
						$messages[] = "invalid name";
					}
					# If there are no errors, it is added into the database
					if (isEmpty($messages)) {
						$studentDAO->add(new Student($data[0], $data[1], $data[2], $data[3], $data[4]));
						$student_processed++;
					} else {
						# Otherwise, it is added to $error
						$errors[] = error("student.csv", $line, $messages);
					}
				}
				$line++;
			}

			// clean up
			fclose($student);
			@unlink($student_path);

			// -------------------------------------------------------------------------------------
			// Course

			// process each line, check for errors, then insert if no errors
			$header = fgetcsv($course);
			$line = 2;
			while (($data = fgetcsv($course)) !== false) {
				$messages = [];
				# strip whitespaces if any
				$data = strip_data($data);
				# Checks if any of the fields are blank
				$common_errors = common_validation($data, $header, $line, "course.csv");
				# If there are blank fields, they are added into $errors and continues on to the next row
				if (!isEmpty($common_errors["message"])) {
					$errors[] = $common_errors;
				} else {
					# checks if it is a valid date
					if (!isNonNegativeFloat($data[4]) || !(checkdate((int)substr($data[4], 4, 2),(int)substr($data[4], 6),(int)substr($data[4],0, 4) ))  ) {
						$messages[] = "invalid exam date";
					}
					# checks if the exam start is valid
					if (!checktime($data[5])) {
						$messages[] = "invalid exam start";
					}
					# checks if the exam end is valid
					if (!checktime($data[6]) || strtotime($data[6]) < strtotime($data[5])) {
						$messages[] = "invalid exam end";
					}
					# checks if title exceeds 100 chars
					if (strlen($data[2]) > 100) {
						$messages[] = "invalid title";
					}
					# checks if description exceeds 1000 chars
					if (strlen($data[3]) > 1000) {
						$messages[] = "invalid description";
					}
					# If there are no errors, it is added into the database
					if (empty($messages)) {
						$courseDAO->add(new Course($data[0], $data[1], $data[2], $data[3], $data[4], $data[5], $data[6]));
						$course_processed++;
					} else {
						# Otherwise, it is added to $error
						$errors[] = error("course.csv", $line, $messages);
					}
				}
				$line++;
			}
			// clean up
			fclose($course);
			@unlink($course_path);

			// -------------------------------------------------------------------------------------
			// Section

			// process each line, check for errors, then insert if no errors
			$header = fgetcsv($section);
			$line = 2;
			while (($data = fgetcsv($section)) !== false) {
				$messages = [];
				# strip whitespaces if any
				$data = strip_data($data);
				# Checks if any of the fields are blank
				$common_errors = common_validation($data, $header, $line, "section.csv");
				# If there are blank fields, they are added into $errors and continues on to the next row
				if (!isEmpty($common_errors["message"])) {
					$errors[] = $common_errors;
				} else {
					# checks if it is a valid course
					if (!$courseDAO->retrieve($data[0])) {
						$messages[] = "invalid course";
					}
					# checks if it starts with the letter 'S' followed by a number within 1-99
					else if (!($data[1][0] == 'S' && substr($data[1], 1) >= 1 && substr($data[1], 1) <= 99 && strlen($data[1]) <= 3) || $data[1][1] == 0) {
						$messages[] = "invalid section";
					}
					# checks if the day is valid
					if (!($data[2] >= 1 && $data[2] <= 7)) {
						$messages[] = "invalid day";
					}
					# checks if the start is valid
					if (!checktime($data[3])) {
						$messages[] = "invalid start";
					}
					# checks if the end is valid
					if (!checktime($data[4]) || strtotime($data[4]) < strtotime($data[3])) {
						$messages[] = "invalid end";
					}
					# checks if instructor exceeds 100 chars
					if (strlen($data[5]) > 100) {
						$messages[] = "invalid instructor";
					}
					# checks if venue exceeds 100 chars
					if (strlen($data[6]) > 100) {
						$messages[] = "invalid venue";
					}
					# checks if size is a positive numeric number
					if (!isNonNegativeInt($data[7]) || $data[7] <= 0) {
						$messages[] = "invalid size";
					}
					# If there are no errors, it is added into the database
					if (isEmpty($messages)) {
						$sectionDAO->add(new Section($data[0], $data[1], $data[2], $data[3], $data[4], $data[5], $data[6], $data[7]));
						$section_processed++;
					} else {
						# Otherwise, it is added to $error
						$errors[] = error("section.csv", $line, $messages);
					}
				}
				$line++;
			}
			// clean up
			fclose($section);
			@unlink($section_path);

			// -------------------------------------------------------------------------------------
			// Prerequisite

			// process each line, check for errors, then insert if no errors
			$header = fgetcsv($prerequisite);
			$line = 2;
			while (($data = fgetcsv($prerequisite)) !== false) {
				$messages = [];
				# strip whitespaces if any
				$data = strip_data($data);
				# Checks if any of the fields are blank
				$common_errors = common_validation($data, $header, $line, "prerequisite.csv");
				# If there are blank fields, they are added into $errors and continues on to the next row
				if (!isEmpty($common_errors["message"])) {
					$errors[] = $common_errors;
				} else {
					# checks if it is a valid course
					if (!$courseDAO->retrieve($data[0])) {
						$messages[] = "invalid course";
					}
					# checks if it is a valid prerequisite
					if (!$courseDAO->retrieve($data[1])) {
						$messages[] = "invalid prerequisite";
					}
					if (isEmpty($messages)) {
						$prerequisiteDAO->add(new Prerequisite($data[0], $data[1]));
						$prerequisite_processed++;
					} else {
						# Otherwise, it is added to $error
						$errors[] = error("prerequisite.csv", $line, $messages);
					}
				}
				$line++;
			}
			// clean up
			fclose($prerequisite);
			@unlink($prerequisite_path);

			// -------------------------------------------------------------------------------------
			// Course Completed

			// process each line, check for errors, then insert if no errors
			$header = fgetcsv($course_completed);
			$line = 2;
			while (($data = fgetcsv($course_completed)) !== false) {
				$messages = [];
				# strip whitespaces if any
				$data = strip_data($data);
				# Checks if any of the fields are blank
				$common_errors = common_validation($data, $header, $line, "course_completed.csv");
				# If there are blank fields, they are added into $errors and continues on to the next row
				if (!isEmpty($common_errors["message"])) {
					$errors[] = $common_errors;
				} else {
					# checks if it is a valid userid
					if (!$studentDAO->retrieve($data[0])) {
						$messages[] = "invalid userid";
					}
					# checks if it is a valid course
					if (!$courseDAO->retrieve($data[1])) {
						$messages[] = "invalid course";
					}
					if (empty($messages)) {
						# List of prerequisites of the course
						$prereq = $prerequisiteDAO->retrieve($data[1]);
						# Checks if there is a prerequisite
						if (!isEmpty($prereq)) {
							# Loop through the array and checks if the prerequisites is within the course_completed of the userid
							foreach ($prereq as $p) {
								if (!in_array($p, $course_completed_DAO->retrieve($data[0]))) {
									$messages[] = "invalid course completed";
									break;
								}
							}
						}
					}

					if (isEmpty($messages)) {
						$course_completed_DAO->add(new CourseCompleted($data[0], $data[1]));
						$course_completed_processed++;
					} else {
						# Otherwise, it is added to $error
						$errors[] = error("course_completed.csv", $line, $messages);
					}
				}
				$line++;
			}
			// clean up
			fclose($course_completed);
			@unlink($course_completed_path);

			// -------------------------------------------------------------------------------------
			// Bid

			// process each line, check for errors, then insert if no errors
			$header = fgetcsv($bid);
			$line = 2;
			while (($data = fgetcsv($bid)) !== false) {
				$messages = [];
				# strip whitespaces if any
				$data = strip_data($data);
				# Checks if any of the fields are blank
				$common_errors = common_validation($data, $header, $line, "bid.csv");
				# If there are blank fields, they are added into $errors and continues on to the next row
				if (!isEmpty($common_errors["message"])) {
					$errors[] = $common_errors;
				} else {
					$messages = array_merge($messages, bid($data, TRUE));
					if (empty($messages)) {
						$bid_processed++;
					} else {
						# Otherwise, it is added to $error
						$errors[] = error("bid.csv", $line, $messages);
					}
				}
				$line++;
			}
			// clean up
			fclose($bid);
			@unlink($bid_path);
		}
	}

	# Sample code for returning JSON format errors. remember this is only for the JSON API. Humans should not get JSON errors.
	$loaded = [
		["bid.csv" => $bid_processed],
		["course.csv" => $course_processed],
		["course_completed.csv" => $course_completed_processed],
		["prerequisite.csv" => $prerequisite_processed],
		["section.csv" => $section_processed],
		["student.csv" => $student_processed]
	];

	if (!isEmpty($errors)) {
		$files = array_column($errors, 'file');
		array_multisort($files, SORT_ASC, $errors);
		$result = [
			"status" => "error",
			"num-record-loaded" => $loaded,
			"error" => $errors
		];
	} else {
		$result = [
			"status" => "success",
			"num-record-loaded" => $loaded
		];
	}

	$pathSegments = explode('/', $_SERVER['PHP_SELF']); # Current url
	$numSegment = count($pathSegments);
	$currentFolder = $pathSegments[$numSegment - 2]; # Current folder
	$page = $pathSegments[$numSegment - 1]; # Current page

	if ($page != "adminIndex.php") {
		header('Content-Type: application/json');
		echo (json_encode($result, JSON_PRETTY_PRINT));
	} else {
		return json_encode($result, JSON_PRETTY_PRINT);
	}
}