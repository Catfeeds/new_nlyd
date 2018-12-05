<?php

/**
 * PHPMailer RFC821 Tmpl email transport class.
 * 邮件发送页面模版
 */
class Tmpl
{
    /**
     * 模版选择
     * @param $type     所需模版类型
     * @param $data     数据
     * @return string
     */
    public function get_tmpl($type,$data){
        switch ($type){
            case 1: //账单明细模版
            case 2: //工单明细模版
                $body = '<!DOCTYPE html>
                            <html lang="en">
                            <head>
                                <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no">
                                <meta content="telephone=no" name="format-detection">
                                <meta charset="UTF-8">
                                <title>Title</title>
                            </head>
                            
                            <body>
                            <div style="font-size: 1.04rem; margin: auto;font-weight: normal;max-width: 500px;position: relative;">
                                <table style="width: 100%;margin-top: 15px;background: #fff;border-spacing: 0;border-collapse: collapse;font-size: 0.91rem;">
                                    <tbody>
                                    <tr>
                                        <td style="border: 1px solid #E3E1DE;padding: 8px;" colspan="2">
                                            <h5 style="color: #D4A37C;font-size: 1.04rem;text-align: center; margin: auto;font-weight: normal;">新都龙凤山公墓管理费收据</h5>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="border: 1px solid #E3E1DE;padding: 8px; width: 85px;color: #A18460;">缴费编号：</td>
                                        <td style="border: 1px solid #E3E1DE;padding: 8px;">'.$data['number'].'</td>
                                    </tr>
                                    <tr>
                                        <td style="border: 1px solid #E3E1DE;padding: 8px; width: 85px;color: #A18460;">缴&nbsp;费&nbsp;人：</td>
                                        <td style="border: 1px solid #E3E1DE;padding: 8px;">'.$data['name'].' '.$data['mobile'].'</td>
                                    </tr>';
                                    if($type == 1){
                                        $tr = '<tr>
                                                    <td style="border: 1px solid #E3E1DE;padding: 8px; width: 85px;color: #A18460;">缴费墓穴：</td>
                                                    <td style="border: 1px solid #E3E1DE;padding: 8px;">'.$data['grave'].'</td>
                                                </tr>
                                                <tr>
                                                    <td style="border: 1px solid #E3E1DE;padding: 8px; width: 85px;color: #A18460;">管理年限：</td>
                                                    <td style="border: 1px solid #E3E1DE;padding: 8px;">'.$data['manage'].'</td>
                                                </tr>';
                                    }elseif ($type == 2) {
                                        $tr = '<tr>
                                                    <td style="border: 1px solid #E3E1DE;padding: 8px; width: 85px;color: #A18460;">维护墓穴：</td>
                                                    <td style="border: 1px solid #E3E1DE;padding: 8px;">'.$data['grave'].'</td>
                                                </tr>';

                                    }

                                    $body .= $tr;
                                    $tmpl_parse =  C('TMPL_PARSE_STRING');
                                    if($_SERVER['HTTP_HOST'] != 'www.miss4ever.com'){
                                        $root_url = 'http://www.miss4ever.com/lfsc/Public/Home/images';
                                    }else{
                                        $root_url = __ROOT_URL__.$tmpl_parse['__IMG__'];
                                    }
                                    $img = $root_url.'/yinz.png';

                                    $html ='<tr>
                                                <td style="border: 1px solid #E3E1DE;padding: 8px; width: 85px;color: #A18460;">收费金额：</td>
                                                <td style="border: 1px solid #E3E1DE;padding: 8px;">¥'.$data['cost'].'</td>
                                            </tr>
                                            <tr>
                                                <td style="border: 1px solid #E3E1DE;padding: 8px; width: 85px;color: #A18460;">缴费时间：</td>
                                                <td style="border: 1px solid #E3E1DE;padding: 8px;">'.$data['time'].'</td>
                                            </tr>
                                            <tr>
                                                <td style="border: 1px solid #E3E1DE;padding: 8px; width: 85px;color: #A18460;">收&nbsp;款&nbsp;方：</td>
                                                <td style="border: 1px solid #E3E1DE;padding: 8px;">永念龙凤山公墓管理处</td>
                                            </tr>
                                            <tr>
                                                <td style="border: 1px solid #E3E1DE;padding: 8px; width: 85px;color: #A18460;" colspan="2">
                                                   <span style="color: #B6232B;font-size: 13px;">备注：凭缴费编号前往龙凤山公墓办公室换取纸质版收据。</span>
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                        <div style="transform: rotate(15deg);-ms-transform: rotate(15deg);-moz-transform: rotate(15deg); -webkit-transform: rotate(15deg); -o-transform: rotate(15deg);display: inline-block;position: absolute;bottom: 60px;right: 6px;width: 120px;height: 80px;"><img style="width: 100%;" src="'.$img.'" alt=""></div>
                                    </div>
                                    </body>
                                </html>';
                                $body .= $html;
                break;
            case 3:
                $body = '';
                break;
            default:
                $body = '';
                break;
        }

        return $body;
    }
}
