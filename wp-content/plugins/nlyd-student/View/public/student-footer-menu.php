<?php
/**
 * 公用脚
 * Created by PhpStorm.
 * User: leo
 * Date: 2018/8/17
 * Time: 11:22
 */
?>

<div class="nl-foot-nav">
    <a class="nl-foot-item active" href="<?=home_url();?>">
        <div class="nl-foot-icon"><i class="iconfont">&#xe62d;</i></div>
        <div class="nl-foot-name">首页</div>
    </a>
    <a class="nl-foot-item" href="#">
        <div class="nl-foot-icon"><i class="iconfont">&#xe631;</i></div>
        <div class="nl-foot-name">训练</div>
    </a>
    <a class="nl-foot-item" href="<?=home_url('/matchs');?>">
        <div class="nl-foot-icon"><i class="iconfont">&#xe692;</i></div>
        <div class="nl-foot-name">比赛</div>
    </a>
    <a class="nl-foot-item" href="#">
        <div class="nl-foot-icon"><i class="iconfont">&#xe630;</i></div>
        <div class="nl-foot-name">考级</div>
    </a>
    <a class="nl-foot-item" href="<?=home_url('/account')?>">
        <div class="nl-foot-icon"><i class="iconfont">&#xe632;</i></div>
        <div class="nl-foot-name">我的</div>
    </a>
</div>
