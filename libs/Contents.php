<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit;

class Contents
{
    /**
     * 内容解析器入口
     * 传入的是经过 Markdown 解析后的文本
     */
     static public function parseContent($data, $widget, $last)
    {
        $text = empty($last) ? $data : $last;
        if ($widget instanceof Widget_Archive) {
        	
        	//FancyBox
	        $text = preg_replace('/<img(.*?)src="(.*?)"(.*?)alt="(.*?)"(.*?)>/s','<center><a data-fancybox="gallery" href="${2}" class="gallery-link" data-caption="${4}"><img${1}src="${2}"${3} alt="${4}" title="${4}"></a><p>${4}</p></center>',$text); 
	        //LazyLoad
		    $text = preg_replace('/<img(.*?)src(.*?)(\/)?>/','<img class="lazy" $1src="/usr/themes/ZERO/images/loading.gif" data-original$2">',$text);
		    //解析友链盒子
	        $reg = '/\[links\](.*?)\[\/links\]/s';
            $rp = '<div class="link-container dalao"><div class="link-box">${1}</div></div>';
            $text = preg_replace($reg,$rp,$text);
		    //解析友链项目
	        $reg = '/\[(.*?)\]\{(.*?)\}\[(.*?)\]\((.*?)\)/s';
            $rp = '<div class="link-box-area"><a href="${3}" target="_blank" title="${1}"><img class="lazy" src="/usr/themes/ZERO/images/loading.gif" data-original="${4}" alt="${1}"><h4>${1}</h4><p>${2}</p></a></div>';
			$text = preg_replace($reg,$rp,$text);
	    	//解析友链盒子2
	        $reg = '/\{links\}(.*?)\{\/links\}/s';
            $rp = '<div class="link-container"><div class="link-box">${1}</div></div>';
            $text = preg_replace($reg,$rp,$text);
		    //解析友链项目2
	        $reg = '/\[(.*?)\]\{(.*?)\}\[(.*?)\]\((.*?)\)/s';
            $rp = '<div class="link-box-area"><a href="${3}" target="_blank" title="${1}"><img class="lazy" src="/usr/themes/ZERO/images/loading.gif" data-original="${4}" alt="${1}"><h4>${1}</h4><p>${2}</p></a></div>';
			$text = preg_replace($reg,$rp,$text);
			//解析b站视频盒子
	        $reg = '/\[bilibili\](.*?)\[\/bilibili\]/s';
            $rp = '<p">${1}</p>';
            $text = preg_replace($reg,$rp,$text);
		    //解析b站视频项目
	        $reg = '/\[(.*?)\]/s';
            $rp = '<iframe class="iframe_video" src="${1}" scrolling="no" border="0" frameborder="no" framespacing="0" allowfullscreen="true"> </iframe>';
					$text = preg_replace($reg,$rp,$text);
					
        }

			
        return $text;
    }

    /**
     * 根据 id 返回对应的对象
     * 此方法在 Typecho 1.2 以上可以直接调用 Helper::widgetById();
     * 但是 1.1 版本尚有 bug，因此单独提出放在这里
     * 
     * @param string $table 表名, 支持 contents, comments, metas, users
     * @return Widget_Abstract
     */
    public static function widgetById($table, $pkId)
    {
        $table = ucfirst($table);
        if (!in_array($table, array('Contents', 'Comments', 'Metas', 'Users'))) {
            return NULL;
        }
        $keys = array(
            'Contents'  =>  'cid',
            'Comments'  =>  'coid',
            'Metas'     =>  'mid',
            'Users'     =>  'uid'
        );
        $className = "Widget_Abstract_{$table}";
        $key = $keys[$table];
        $db = Typecho_Db::get();
        $widget = new $className(Typecho_Request::getInstance(), Typecho_Widget_Helper_Empty::getInstance());
        
        $db->fetchRow(
            $widget->select()->where("{$key} = ?", $pkId)->limit(1),
                array($widget, 'push'));
        return $widget;
    }

    /**
     * 输出完备的标题
     */
    public static function title(Widget_Archive $archive)
    {
        $archive->archiveTitle(array(
            'category'  =>  '分类 %s 下的文章',
            'search'    =>  '包含关键字 %s 的文章',
            'tag'       =>  '标签 %s 下的文章',
            'author'    =>  '%s 发布的文章'
        ), '', ' - ');
        Helper::options()->title();
    }

    /**
     * 返回上一篇文章
     */
    public static function getPrev($archive)
    {
        $db = Typecho_Db::get();
        $content = $db->fetchRow($db->select()->from('table.contents')->where('table.contents.created < ?', $archive->created)
            ->where('table.contents.status = ?', 'publish')
            ->where('table.contents.type = ?', $archive->type)
            ->where('table.contents.password IS NULL')
            ->order('table.contents.created', Typecho_Db::SORT_DESC)
            ->limit(1));
        
        if($content) {
            return self::widgetById('Contents', $content['cid']);    
        }else{
            return NULL;
        }
    }

    /**
     * 返回下一篇文章
     */
    public static function getNext($archive)
    {
        $db = Typecho_Db::get();
        $content = $db->fetchRow($db->select()->from('table.contents')->where('table.contents.created > ? AND table.contents.created < ?',
            $archive->created, Helper::options()->gmtTime)
                ->where('table.contents.status = ?', 'publish')
                ->where('table.contents.type = ?', $archive->type)
                ->where('table.contents.password IS NULL')
                ->order('table.contents.created', Typecho_Db::SORT_ASC)
                ->limit(1));

        if($content) {
            return self::widgetById('Contents', $content['cid']);    
        }else{
            return NULL;
        }
    }

    /**
     * 最近评论，过滤引用通告，过滤博主评论
     */
    public static function getRecentComments($num = 10)
    {
        $comments = array();

        $db = Typecho_Db::get();
        $rows = $db->fetchAll($db->select()->from('table.comments')->where('table.comments.status = ?', 'approved')
            ->where('type = ?', 'comment')
            ->where('ownerId <> authorId')
            ->order('table.comments.created', Typecho_Db::SORT_DESC)
            ->limit($num));

        foreach ($rows as $row) {
            $comment =  self::widgetById('Comments', $row['coid']);
            $comments[] = $comment;
        }

        return $comments;
    }
    
 
}