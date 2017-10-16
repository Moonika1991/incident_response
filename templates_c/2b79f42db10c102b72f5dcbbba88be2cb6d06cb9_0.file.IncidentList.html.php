<?php
/* Smarty version 3.1.32-dev-1, created on 2017-10-16 20:41:57
  from "C:\xampp\htdocs\incident_response\app\incident\list\IncidentList.html" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.32-dev-1',
  'unifunc' => 'content_59e4fd751cc0d2_16625794',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '2b79f42db10c102b72f5dcbbba88be2cb6d06cb9' => 
    array (
      0 => 'C:\\xampp\\htdocs\\incident_response\\app\\incident\\list\\IncidentList.html',
      1 => 1508170893,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_59e4fd751cc0d2_16625794 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_loadInheritance();
$_smarty_tpl->inheritance->init($_smarty_tpl, true);
?>


<?php 
$_smarty_tpl->inheritance->instanceBlock($_smarty_tpl, 'Block_2304959e4fd751c8932_88699213', 'top');
$_smarty_tpl->inheritance->endChild($_smarty_tpl, ($_smarty_tpl->tpl_vars['conf']->value->root_path).("/templates/main.html"));
}
/* {block 'top'} */
class Block_2304959e4fd751c8932_88699213 extends Smarty_Internal_Block
{
public $subBlocks = array (
  'top' => 
  array (
    0 => 'Block_2304959e4fd751c8932_88699213',
  ),
);
public function callBlock(Smarty_Internal_Template $_smarty_tpl) {
?>


    <table>
    <tr>
        <th>Title</th>
        <th>Date</th>
        <th>Team</th>
        <th>Solved</th>
    </tr>
    </table>
<?php
}
}
/* {/block 'top'} */
}
