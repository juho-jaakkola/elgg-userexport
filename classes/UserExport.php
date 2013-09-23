<?php
/**
 * Class that exports users to a csv file
 */
class UserExport {
	/**
	 * @var int $offset
	 */
	private $offset;

	/**
	 * @var int $limit
	 */
	private $limit;

	/**
	 * @var int $userCount
	 */
	private $userCount;

	/**
	 * @var string $filedir
	 */
	private $filedir;

	/**
	 * @var string $filename
	 */
	private $filename;

	/**
	 * @var resource $file
	 */
	private $file;

	/**
	 * Set default values
	 */
	public function __construct() {
		$this->limit = 50;
		$this->filedir = elgg_get_data_path() . 'userexport';
		$this->filename = $this->filedir . "/export.csv";
		$this->usercount = 0; 
	}

	/**
	 * Generate new csv rows
	 * 
	 * @return boolean
	 */
	public function generate() {
		if ($this->offset == 0) {
			// Starting to generate a new file
			$this->prepareFile();
			$this->createFileHeadings();
		}

		$users = $this->getUserBatch();

		foreach ($users as $user) {
			$fields = $this->userToArray($user);
			$this->writeToCSV($fields);
			$this->usercount++;
		}

		$this->closeFile();

		return true;
	}

	/**
	 * Generate an array of the user's profile fields
	 * 
	 * @param ElggUser $user The user to process
	 * @return array $values Array of user profile fields
	 */
	private function userToArray($user) {
		$values = array();
		foreach ($this->fields as $field) {
			switch ($field) {
				case 'admin':
					// Checking admin from metadata causes a deprecation warning
					$value = $user->isAdmin();
					break;
				case 'days_of_last_login':
					// Count the days from last login
					if ((int) $user->last_login > 0) {
						$seconds = time() - (int) $user->last_login;
						$value = round($seconds / 60 / 60 / 24);
					} else {
						$value = elgg_echo('never');
					}
					break;
				default:
					if (is_array($user->$field)) {
						// Concatenate fields with multiple values into a single string
						$value = implode(", ", $user->$field);
					} else {
						$value = $user->$field;
					}
				break;
			}

			$values[$field] = $value;
		}

		// Parameters for the plugin hook
		$params = array(
			'entity' => $user,
			'fields' => $this->fields,
		);

		// Allow other plugins to add their own fields
		$values = elgg_trigger_plugin_hook('row:values', 'userexport', $params, $values);

		return $values;
	}

	/**
	 * Open file handle
	 */
	private function openFile() {
		$this->file = fopen($this->filename, 'a');
	}

	/**
	 * Close the file handle
	 */
	private function closeFile() {
		return fclose($this->file);
	}

	/**
	 * Set the fields that are included in the csv file
	 * 
	 * @param array $fields Array of profile field names
	 */
	public function setFields(array $fields) {
		$this->fields = $fields;
	}

	/**
	 * Set user query offset
	 * 
	 * @param int $offset User query offset
	 */
	public function setOffset($offset) {
		$this->offset = $offset;
	}

	/**
	 * Get new amount of added rows
	 * 
	 * @return int $usercount
	 */
	public function getNumberOfAddedRows() {
		return $this->usercount;
	}

	/**
	 * Return a batch of users
	 * 
	 * @return ElggUser[] $users An array of ElggUser objects
	 */
	private function getUserBatch() {
		// Include also hidden (disabled) users to the export
		$hidden_status = access_get_show_hidden_status();
		access_show_hidden_entities(true);

		// Ignore access settings to get all users
		elgg_set_ignore_access(true);

		$users = elgg_get_entities(array(
			'type' => 'user',
			'limit' => $this->limit,
			'offset' => $this->offset,
		));

		// Set access level to normal
		elgg_set_ignore_access(false);

		// Set hidden status to normal
		access_show_hidden_entities($hidden_status);

		return $users;
	}

	/**
	 * Make sure we are able to write to a new and blank file
	 * 
	 * @throws Exception
	 */
	public function prepareFile() {
		// Check if the file do exists
		if (!is_dir($this->filedir)) {
			// Create empty directory for the userexport
			if (!mkdir($this->filedir, 0755)) {
				// Creating the directory failed
				throw new Exception(elgg_echo('userexport:error:nofiledir'));
			}
		}

		// Delete any existing file
		unlink($this->filename);
	}

	/**
	 * Add each field name as the first row of the csv file 
	 */
	private function createFileHeadings() {
		// Allow other plugins to add their own column headers
		$headings = elgg_trigger_plugin_hook('row:headers', 'userexport', array(), $this->fields);

		// Attempt to translate the fields
		foreach ($headings as $key => $field) {
			$lang_string = "profile:{$field}";
			$heading = elgg_echo($lang_string);

			// No translation was found, fall back to the original string
			if ($heading === $lang_string) {
				$heading = elgg_echo($field);
			}

			$headings[$key] = $heading;
		}

		$this->writeToCSV($headings);
	}

	/**
	 * Format array to a CSV line and write it to the file
	 * 
	 * @return int|boolean Length of the written string or FALSE on failure
	 */
	private function writeToCSV(array $row) {
		if (!$this->file) {
			$this->openFile();
		}

		return fputcsv($this->file, $row);
	}
}