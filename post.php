<?php

if (!defined('__TYPECHO_ROOT_DIR__')) exit;
$this->need('includes/head.php');
$this->need('includes/header.php');

?>

<!--页面主要内容-->
    <!-- <title hidden>
        <?php Contents::title($this); ?>
    </title>-->
    
  <div class="wrapper container">
        <div class="contents-wrap post-body"> <!--start .contents-wrap-->
            <section id="post" class="float-up">
                <article class="post yue" itemscope itemtype="http://schema.org/Article">
                    <h1 hidden itemprop="name"><?php $this->title(); ?></h1>
                    <span hidden itemprop="author"><?php $this->author(); ?></span>
                    <time hidden datetime="<?php echo date('c', $this->created); ?>" itemprop="datePublished"><?php echo date('Y-m-d', $this->created); ?></time>
                    <div class="headline" itemprop="headline"><span><?php echo $this->fields->excerpt;?></span></div>
                    

  <?php $postCheck = Utils::isOutdated($this); if($postCheck["is"] && $this->is('post')): ?>
				<blockquote><p>本文编写于 <?php echo $postCheck["created"]; ?> 天前，最后修改于 <?php echo $postCheck["updated"]; ?> 天前，其中某些信息可能已经过时。</p></blockquote>
          <?php endif; ?>
               
                    <div itemprop="articleBody" class="full">
                        <?php $this->content(); ?>
                    </div>
                </article>
            </section>
  <p class="post-tags"><i class="iconfont icon-tags"></i> <?php $this->tags(' ', true, '<span>此文章还未分配标签</span>'); ?></p>
                <!--打赏-->
                <?php $this->need('includes/reward.php'); ?>
   
                <!--版权信息-->
                <?php $this->need('includes/copyright.php'); ?>

                <!--评论区-->
                <?php $this->need('includes/comments.php'); ?>
                
        </div>  </div> <!--end .contents-wrap-->
     

        
        
<?php $this->need('includes/footer.php'); ?>