<?php
/**
 * 公用脚
 * Created by PhpStorm.
 * User: leo
 * Date: 2018/8/17
 * Time: 11:22
 */
?>

<div class="nl-foot-nav flex-h">
    <a class="nl-foot-item flex1 disabled_a <?=CONTROLLER == 'student' ? 'active':'';?>" href="<?=home_url('/student/index');?>">
        <div class="nl-foot-icon">
            <div class="footer-home"></div>
        </div>
        <div class="nl-foot-name"><?=__('首页', 'nlyd-student')?></div>
    </a>
    <a class="nl-foot-item flex1 <?=CONTROLLER == 'train' ? 'active':'';?> " href="<?=home_url('/trains');?>">
        <div class="nl-foot-icon">
            <div class="footer-train"></div>
        </div>
        <div class="nl-foot-name"><?=__('训练', 'nlyd-student')?></div>
    </a>
    <a class="nl-foot-item flex1 <?=CONTROLLER == 'matchs' ? 'active':'';?> " href="<?=home_url('/matchs');?>">
        <div class="nl-foot-icon">
            <div class="footer-match"></div>
        </div>
        <div class="nl-foot-name"><?=__('比赛', 'nlyd-student')?></div>
    </a>
    <a class="nl-foot-item flex1 disabled_a <?=CONTROLLER == 'grading' ? 'active':'';?>">
        <div class="nl-foot-icon">
            <div class="footer-kaoji"></div>
        </div>
        <div class="nl-foot-name"><?=__('考级', 'nlyd-student')?></div>
    </a>
    <a class="nl-foot-item flex1 <?=CONTROLLER == 'account' ? 'active':'';?>" href="<?=home_url('account')?>">
        <div class="nl-foot-icon">
            <div class="footer-user"></div>
        </div>
        <div class="nl-foot-name"><?=__('我的', 'nlyd-student')?></div>
    </a>
</div>
