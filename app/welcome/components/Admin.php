<?php namespace App\Welcome\Components;

use Coffee\Foundation\Component;

class Admin extends Component
{
	/* 是否为管理员 */
	public function is($uid)
	{
		return (bool) $this->db("SELECT value FROM usermeta WHERE `key`='is_admin' AND `uid`=?",$uid)->one();
	}

	// 检查备份目录
	private function checkBackupFolder()
	{
		$dir = STORAGE.'/backup';
		if (! file_exists ($dir)) {
			if (! is_writeable (dirname ($dir))) {
				die ('Backup folder must be writeable to continue.');
			}
			mkdir ($dir);
			chmod ($dir, 0777);
		}
		return $dir;
	}

	// 获取数据库备份文件
	public function getBackupFiles()
	{
		$backupFolder = $this->checkBackupFolder();
		// 读取文件夹
		$array = array();
		if($handle=opendir($backupFolder)){
			$no=1;
			while(false!==($file=readdir($handle))){
				if (strpos($file,'.sql')!==false){
					$array[$no]['no']=$no;
					$array[$no]['file']=$file;
					$array[$no]['created']=date('Y-m-d H:i:s',filemtime($backupFolder.'/'.$file));
					$no++;
				}
			}
			closedir($handle);
		}
		return $array;
	}

	// 导出指定数据库备份文件
	public function exportBackup()
	{
		$dir = $this->checkBackupFolder();
		$sql = $this->db()->export();
		$filename = date('YmdHis') . mt_rand(0,99999) . '.sql';
		return file_put_contents($dir .'/'. $filename, $sql);
	}

	// 还原指定数据库备份文件
	public function restoreBackup($file)
	{
		if(empty($file)) return false;
		$dir = $this->checkBackupFolder();
		$filename = $dir .'/'. $file;
		if(file_exists($filename)){
			$query = file_get_contents($filename);
			$this->db()->exec($query);
			return true;
		}
		return false;
	}

	// 删除指定数据库备份文件
	public function deleteBackup($file)
	{
		if(empty($file)) return false;
		$dir = $this->checkBackupFolder();
		$filename = $dir .'/'. $file;

		if(file_exists($filename)){
			return unlink($filename);
		}
		return false;
	}
}