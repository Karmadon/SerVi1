<?php
	/**
	 * Created by PhpStorm.
	 * User: strem
	 * Date: 30.08.2015
	 * Time: 14:19
	 */


	require_once __DIR__ . '/../Common/Records.php';


	class Device extends
		Records
	{
		public
			$id,
			$name,
			$description,
			$categoryID,
			$categorySTR,
			$typeID,
			$typeSTR,
			$serial,
			$manufacturerID,
			$manufacturerSTR,
			$modelID,
			$modelSTR,
			$ownerID,
			$ownerSTR,
			$lastEdit,
			$deleted,
			$meta;


		public function createRecordInDB()
		{
			if (!isset($this->connection))
			{
				$this->getConnection();
			}

			global $insertID;


			queryMysql("INSERT INTO dev_category (NAME) VALUES('$this->categorySTR') ON DUPLICATE KEY UPDATE NAME = '$this->categorySTR'");
			if ($insertID === 0)
			{
				$result = queryMysql("SELECT ID FROM dev_category WHERE NAME = '$this->categorySTR'");
				$this->row = $result->fetch_array(MYSQLI_ASSOC);
				$this->categoryID = $this->row ['ID'];
			} else
			{
				$this->categoryID = $insertID;
			}

			queryMysql("INSERT INTO dev_manufacturer (NAME) VALUES('$this->manufacturerSTR') ON DUPLICATE KEY UPDATE NAME = '$this->manufacturerSTR'");
			if ($insertID === 0)
			{
				$result = queryMysql("SELECT ID FROM dev_manufacturer WHERE NAME = '$this->manufacturerSTR'");
				$this->row = $result->fetch_array(MYSQLI_ASSOC);
				$this->manufacturerID = $this->row ['ID'];
			} else
			{
				$this->manufacturerID = $insertID;
			}

			queryMysql("INSERT INTO dev_models (NAME) VALUES('$this->modelSTR') ON DUPLICATE KEY UPDATE NAME = '$this->modelSTR'");
			if ($insertID === 0)
			{
				$result = queryMysql("SELECT ID FROM dev_models WHERE NAME = '$this->modelSTR'");
				$this->row = $result->fetch_array(MYSQLI_ASSOC);
				$this->modelID = $this->row ['ID'];
			} else
			{
				$this->modelID = $insertID;
			}

			queryMysql("INSERT INTO dev_type (NAME) VALUES('$this->typeSTR') ON DUPLICATE KEY UPDATE NAME = '$this->typeSTR'");
			if ($insertID === 0)
			{
				$result = queryMysql("SELECT ID FROM dev_type WHERE NAME = '$this->typeSTR'");
				$this->row = $result->fetch_array(MYSQLI_ASSOC);
				$this->typeID = $this->row ['ID'];
			} else
			{
				$this->typeID = $insertID;
			}


			$query = "INSERT INTO devices (ID, STICKER, NAME, DESCRIPTION, MANUFACTURER_ID, CATEGORY_ID, TYPE_ID, SERIAL, MODEL_ID, OWNER_ID, LAST_EDIT, DELETED, META)
VALUES
	(
		NULL,
		'$this->ownerID.__',
		'$this->name',
		'$this->description',
		'$this->manufacturerID',
		'$this->categoryID',
		'$this->typeID',
		'$this->serial',
		'$this->modelID',
		'$this->ownerID',
		'$this->lastEdit',
		0,
		'$this->meta')";

			$result = queryMysql($query);
			$this->id = $insertID;

			if (!$result)
			{
				makeRecordInHistory('6', '1', $this->connection->insert_id, "Ошибка при вставке записи $this->id");
				die ($this->connection->error);
			} else
			{
				makeRecordInHistory('1', '1', $this->id, "Добавлено устройство <b>$this->name</b>");

				return 1;
			}
		}

		public function getRecordInfo(&$deviceID)
		{

			$result = queryMysql("
SELECT
  dev.ID              AS ID,
  dev.STICKER         AS STICKER,
  dev.NAME            AS NAME,
  dev.DESCRIPTION     AS DESCRIPTION,
  dev.SERIAL          AS SERIAL,
  dev.LAST_EDIT       AS LAST_EDIT,
  dev.DELETED         AS DELETED,
  dev.META            AS META,
  man.NAME            AS MANUFACTURER,
  cat.NAME            AS CATEGORY,
  modl.NAME           AS MODEL,
  typ.NAME            AS TYPE,
  cli.SCREEN_NAME     AS OWNER,
  dev.OWNER_ID        AS OWNER_ID
FROM devices dev
  INNER JOIN dev_manufacturer man ON dev.MANUFACTURER_ID = man.ID
  INNER JOIN dev_category cat ON dev.CATEGORY_ID = cat.ID
  INNER JOIN dev_models modl ON dev.MODEL_ID = modl.ID
  INNER JOIN dev_type typ ON dev.TYPE_ID = typ.ID
  INNER JOIN clients cli ON dev.OWNER_ID = cli.ID
WHERE dev.ID = '$deviceID' AND dev.DELETED = '0'"
			);

			if ($result->num_rows)
			{
				return $result->fetch_array(MYSQLI_ASSOC);
			} else
			{
				return 0;
			}
		}

		public function editRecordInDB($recordID)
		{

			if (!isset($this->connection))
			{
				$this->getConnection();
			}
			global $insertID;

			queryMysql("INSERT INTO dev_category (NAME) VALUES('$this->categorySTR') ON DUPLICATE KEY UPDATE NAME = '$this->categorySTR'");
			if ($insertID === 0)
			{
				$result = queryMysql("SELECT ID FROM dev_category WHERE NAME = '$this->categorySTR'");
				$this->row = $result->fetch_array(MYSQLI_ASSOC);
				$this->categoryID = $this->row ['ID'];
			} else
			{
				$this->categoryID = $insertID;
			}

			queryMysql("INSERT INTO dev_manufacturer (NAME) VALUES('$this->manufacturerSTR') ON DUPLICATE KEY UPDATE NAME = '$this->manufacturerSTR'");
			if ($insertID === 0)
			{
				$result = queryMysql("SELECT ID FROM dev_manufacturer WHERE NAME = '$this->manufacturerSTR'");
				$this->row = $result->fetch_array(MYSQLI_ASSOC);
				$this->manufacturerID = $this->row ['ID'];
			} else
			{
				$this->manufacturerID = $insertID;
			}

			queryMysql("INSERT INTO dev_models (NAME) VALUES('$this->modelSTR') ON DUPLICATE KEY UPDATE NAME = '$this->modelSTR'");
			if ($insertID === 0)
			{
				$result = queryMysql("SELECT ID FROM dev_models WHERE NAME = '$this->modelSTR'");
				$this->row = $result->fetch_array(MYSQLI_ASSOC);
				$this->modelID = $this->row ['ID'];
			} else
			{
				$this->modelID = $insertID;
			}

			queryMysql("INSERT INTO dev_type (NAME) VALUES('$this->typeSTR') ON DUPLICATE KEY UPDATE NAME = '$this->typeSTR'");
			if ($insertID === 0)
			{
				$result = queryMysql("SELECT ID FROM dev_type WHERE NAME = '$this->typeSTR'");
				$this->row = $result->fetch_array(MYSQLI_ASSOC);
				$this->typeID = $this->row ['ID'];
			} else
			{
				$this->typeID = $insertID;
			}

			$query = "UPDATE devices
SET
	NAME            = '$this->name',
	DESCRIPTION     = '$this->description',
	MANUFACTURER_ID = '$this->manufacturerID',
	CATEGORY_ID     = '$this->categoryID',
	TYPE_ID         = '$this->typeID',
	SERIAL          = '$this->serial',
	MODEL_ID        = '$this->modelID',
	OWNER_ID        = '$this->ownerID',
	LAST_EDIT       = '$this->lastEdit',
	DELETED         = '$this->deleted',
	META            = '$this->meta'

WHERE ID = '$recordID'";
			$result = queryMysql($query);
			if (!$result)
			{
				makeRecordInHistory('6', '1', $recordID, "Ошибка при изменении записи $recordID");
				die ($this->connection->error);
			} else
			{
				makeRecordInHistory('2', '1', $recordID, "Изменено Устройство <b>$this->name</b>");
				Echo <<<HTML

	<div class = 'col-lg-7'>
<div id = "myAlert" class = "alert alert-success">
   <a href = "#" class = "close" data-dismiss = "alert">&times;</a>
   <strong>Изменения приняты!</strong>.
</div>
</div>


<script type = "text/javascript">
   $(function(){
      $("#myAlert").bind('closed.bs.alert', function () {
         alert("Alert message box is closed.");
      });
   });
</script>
HTML;
			}
		}

		public function initFromDB(&$deviceID)
		{
			if (!isset($this->connection))
			{
				$this->getConnection();
			}

			$querry = "SELECT
  dev.ID              AS ID,
  dev.STICKER         AS STICKER,
  dev.NAME            AS NAME,
  dev.DESCRIPTION     AS DESCRIPTION,
  dev.MANUFACTURER_ID AS MANUFACTURER_ID,
  dev.CATEGORY_ID     AS CATEGORY_ID,
  dev.TYPE_ID         AS TYPE_ID,
  dev.SERIAL          AS SERIAL,
  dev.MODEL_ID        AS MODEL_ID,
  dev.OWNER_ID        AS OWNER_ID,
  dev.LAST_EDIT       AS LAST_EDIT,
  dev.DELETED         AS DELETED,
  dev.META            AS META,
  man.NAME            AS MANUFACTURER,
  cat.NAME            AS CATEGORY,
  modl.NAME           AS MODEL,
  typ.NAME            AS TYPE,
  cli.SCREEN_NAME     AS OWNER

FROM devices dev
  INNER JOIN dev_manufacturer man ON dev.MANUFACTURER_ID = man.ID
  INNER JOIN dev_category cat ON dev.CATEGORY_ID = cat.ID
  INNER JOIN dev_models modl ON dev.MODEL_ID = modl.ID
  INNER JOIN dev_type typ ON dev.TYPE_ID = typ.ID
  INNER JOIN clients cli ON dev.OWNER_ID = cli.ID
WHERE dev.ID = '$deviceID' AND dev.DELETED = '0'
";

			$result = queryMysql($querry);

			if ($result->num_rows)
			{
				$this->num = $result->num_rows;
				$this->row = $result->fetch_array(MYSQLI_ASSOC);

				$this->id = $this->row ['ID'];
				$this->name = $this->row ['NAME'];
				$this->sticker = $this->row ['STICKER'];
				$this->description = $this->row ['DESCRIPTION'];
				$this->manufacturerID = $this->row ['MANUFACTURER_ID'];
				$this->manufacturerSTR = $this->row ['MANUFACTURER'];
				$this->categoryID = $this->row ['CATEGORY_ID'];
				$this->categorySTR = $this->row ['CATEGORY'];
				$this->typeID = $this->row ['TYPE_ID'];
				$this->typeSTR = $this->row ['TYPE'];
				$this->modelID = $this->row ['MODEL_ID'];
				$this->modelSTR = $this->row ['MODEL'];
				$this->serial = $this->row ['SERIAL'];
				$this->deleted = $this->row ['DELETED'];
				$this->lastEdit = $this->row ['LAST_EDIT'];
				$this->ownerID = $this->row ['OWNER_ID'];
				$this->ownerSTR = $this->row ['OWNER'];
				$this->meta = $this->row ['META'];

			} else
			{
				$result->close();

				return 0;
			}
			$result->close();

			return 1;
		}

		public function initFromPOST()
		{
			date_default_timezone_set('Europe/Kiev');

			$this->id = post('deviceid');
			$this->name = post('devicename');
			$this->manufacturerSTR = post('devicemanufac');
			$this->modelSTR = post('devicemodel');
			$this->serial = post('deviceserial');
			$this->description = post('devicedescription');
			$this->categorySTR = post('devicecategory');
			$this->typeSTR = post('devicetype');
			$this->ownerID = post('deviceowner');
			$this->lastEdit = date('Y-m-d H:i:s');


			$this->meta = (post('devicemeta') === 'on') ? 1 : 0;
		}

		public function initMetaObject()
		{
			$this->id = '0';
			$this->name = time();
			$this->manufacturerID = '1';
			$this->modelID = '1';
			$this->serial = 'SN' . time();
			$this->description = 'This is Automated Meta Device';
			$this->categoryID = '1';
			$this->typeID = '1';
			$this->ownerID = '1';
			$this->lastEdit = date('Y-m-d H:i:s');
			$this->meta = '1';
		}

		public function deleteRecordFromDB($recordID)
		{
			date_default_timezone_set('Europe/Kiev');
			$deviceLastEditDate = date('Y-m-d H:i:s');
			$result = queryMysql("UPDATE devices SET DELETED='1',LAST_EDIT='$deviceLastEditDate' WHERE ID='$devicenum' AND DELETED = '0'"
			);
			if (!$result)
			{
				makeRecordInHistory('6', '1', $recordID, "Ошибка при удалении записи $recordID");
				die ($this->connection->error);
			} else
			{
				makeRecordInHistory('3', '1', $recordID, "Запись Удалена $recordID");
				echo 'Deleted!';
			}
		}


	}