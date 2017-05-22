<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 上传文件能用的方法
/*public __construct ( string $file_name )
public int getATime ( void ) //获取文件的最后访问时间
public string getBasename ([ string $suffix ] ) //获取文件的没有路径信息的基本名称，参数可以为文件后缀，若有参数则返回没有该后缀的文件基本名称。
public int getCTime ( void ) //返回文章最后一次变更的时间戳。
public string getExtension ( void ) //获取文件扩展名
public SplFileInfo getFileInfo ([ string $class_name ] ) //以对象的形式返回文件路径和名称
public string getFilename ( void ) //获取文件名称，不带路径
public int getGroup ( void ) //获取文件所在组，返回组id
public int getInode ( void ) //获取文件索引节点
public string getLinkTarget ( void ) //获取文件链接目标
public int getMTime ( void ) //获取最后修改时间
public int getOwner ( void ) //获取文件的所有者
public string getPath ( void ) //获取文件路径，不带文件名和最后的斜杠
public SplFileInfo getPathInfo ([ string $class_name ] ) //返回路径对象
public string getPathname ( void ) //获取文件路径
public int getPerms ( void ) //获取文件权限
public string getRealPath ( void ) //获取文件绝对路径，若文件不存在，返回false
public int getSize ( void ) //返回文件大小，单位字节
public string getType ( void ) //返回文件类型，可能是 file, link, dir
public bool isDir ( void ) //判断是否是目录，是放回true否则返回false
public bool isExecutable ( void ) //判断文件是否可执行，返回true，否则返回false
public bool isFile ( void ) //如果文件存在且是一个普通文件（不是链接），返回true，否则返回false
public bool isLink ( void ) //判断文件是否是连接，不是返回false
public bool isReadable ( void ) //判断文件是否可读，可读返回true
public bool isWritable ( void ) //判断文件是否可写，可写返回true
public SplFileObject openFile ([ string $open_mode = "r" [, bool $use_include_path = false [, resource $context = NULL ]]] ) //获取文件对象信息
public void setFileClass ([ string $class_name = "SplFileObject" ] )
public void setInfoClass ([ string $class_name = "SplFileInfo" ] )
public void __toString ( void ) //以字符串的形式返回文件路径及名称*/

/*
 * 功能：用递归删除不为空的函数
 * @param $dir_name  要删除的目录
 * 删除成功return true 反之 false
 * */
//删除目录
function delete_dir($dir_name){
    // 打开目录
    //判断目录是否存在，存在才执行
    if(file_exists($dir_name)){
        $handle = opendir($dir_name);

        //遍历目录文件
        while (($file=readdir($handle))!==FALSE){
            //滤掉 "." 和 ".."目录
            if($file!='.' && $file!='..'){
                // 子目录
                $file = $dir_name.DIRECTORY_SEPARATOR.$file;
                //判断是否为目录
                if(is_dir($file)){
                    //递归调用
                    delete_dir($file);
                }else {
                    //删除文件
                    unlink($file);
                }
            }
        }
        //关闭
        closedir($handle);
        //删除目录
        if(rmdir($dir_name)){
            return true;
        }else {
            return false;
        }
    }
}

