<?php  defined('C5_EXECUTE') or die(_("Access Denied.")); ?> 
<?php 
Loader::model('file_set');

$searchFields = array(
	'' => '** ' . t('Fields'),
	'size' => t('Size'),
	'type' => t('Type'),
	'extension' => t('Extension'),
	'date_added' => t('Added Between'),
);

if ($_REQUEST['fType'] != false) {
	unset($searchFields['type']);
}
if ($_REQUEST['fExtension'] != false) {
	unset($searchFields['extension']);
}

Loader::model('file_attributes');
$searchFieldAttributes = FileAttributeKey::getSearchableList();
foreach($searchFieldAttributes as $ak) {
	$searchFields[$ak->getAttributeKeyID()] = $ak->getAttributeKeyDisplayHandle();
}

$ext1 = FileList::getExtensionList();
$extensions = array();
foreach($ext1 as $value) {
	$extensions[$value] = $value;
}

$t1 = FileList::getTypeList();
$types = array();
foreach($t1 as $value) {
	$types[$value] = FileType::getGenericTypeText($value);
}

$s1 = FileSet::getMySets();

?>

<?php  $form = Loader::helper('form'); ?>

	
	<div id="ccm-<?php echo $searchInstance?>-search-field-base-elements" style="display: none">
	
		<span class="ccm-search-option" search-field="size">
		<?php echo $form->text('size_from', array('style' => 'width: 30px'))?>
		<?php echo t('to')?>
		<?php echo $form->text('size_to', array('style' => 'width: 30px'))?>
		KB
		</span>
	
		<span class="ccm-search-option"  search-field="type">
		<?php echo $form->select('type', $types)?>
		</span>
	
		<span class="ccm-search-option"  search-field="extension">
		<?php echo $form->select('extension', $extensions)?>
		</span>

		<span class="ccm-search-option"  search-field="date_added">
		<?php echo $form->text('date_from', array('style' => 'width: 86px'))?>
		<?php echo t('to')?>
		<?php echo $form->text('date_to', array('style' => 'width: 86px'))?>
		</span>
		
		<?php  foreach($searchFieldAttributes as $sfa) { 
			$sfa->render('search'); ?>
		<?php  } ?>
		
	</div>
	<?php  /*
	
	<?php  if ($searchType == 'DASHBOARD') { ?>
		<form method="get" id="ccm-file-advanced-search" action="<?php echo $this->url('/dashboard/files/search')?>">
	<?php  } else { ?>
		<form method="get" id="ccm-file-advanced-search" action="<?php echo REL_DIR_FILES_TOOLS_REQUIRED?>/files/search_results">
	<?php  } ?>
	
	*/
	?>

	<form method="get" id="ccm-<?php echo $searchInstance?>-advanced-search" action="<?php echo REL_DIR_FILES_TOOLS_REQUIRED?>/files/search_results">

<input type="hidden" name="searchInstance" value="<?php echo $searchInstance?>" />
	
<div id="ccm-<?php echo $searchInstance?>-search-advanced-fields" class="ccm-search-advanced-fields" >
	
		<input type="hidden" name="submit_search" value="1" />
	<?php 	/** 
		 * Here are all the things that could be passed through the asset library that we need to account for, as hidden form fields
		 */
		print $form->hidden('fType'); 
		print $form->hidden('fExtension'); 
		print $form->hidden('ccm_order_dir', $searchRequest['ccm_order_dir']); 
		print $form->hidden('ccm_order_by', $searchRequest['ccm_order_by']); 
		print $form->hidden('fileSelector', $fileSelector); 
	?>	
		<div id="ccm-search-box-title">
			<?php  if ($_REQUEST['fType'] != false) { ?>
				<div class="ccm-file-manager-pre-filter"><?php echo t('Only displaying %s files.', FileType::getGenericTypeText($_REQUEST['fType']))?></div>
			<?php  } else if ($_REQUEST['fExtension'] != false) { ?>
				<div class="ccm-file-manager-pre-filter"><?php echo t('Only displaying files with extension .%s.', $_REQUEST['fExtension'])?></div>
			<?php  } ?>
	
			<img src="<?php echo ASSETS_URL_IMAGES?>/throbber_white_16.gif" width="16" height="16" class="ccm-search-loading" id="ccm-<?php echo $searchInstance?>-search-loading" />
			
			<h2><?php echo t('Search')?></h2>			
		</div>
		
		<div id="ccm-search-advanced-fields-inner">
			<div class="ccm-search-field">
				<table border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td width="100%">
					<?php echo $form->text('fKeywords', $searchRequest['fKeywords'], array('style' => 'width:200px')); ?>
					</td>
				</tr>
				</table>
			</div>
		
			<div class="ccm-search-field">
				<table border="0" cellspacing="0" cellpadding="0" width="100%">
				<tr>
					<td style="white-space: nowrap" align="right"><div style="width: 85px; padding-right:5px"><?php echo t('Results Per Page')?></div></td>
					<td width="100%">
						<?php echo $form->select('numResults', array(
							'10' => '10',
							'25' => '25',
							'50' => '50',
							'100' => '100',
							'500' => '500'
						), $searchRequest['numResults'], array('style' => 'width:65px'))?>
					</td>
					<td><a href="javascript:void(0)" id="ccm-<?php echo $searchInstance?>-search-add-option"><img src="<?php echo ASSETS_URL_IMAGES?>/icons/add.png" width="16" height="16" /></a></td>
				</tr>	
				</table>
			</div>
			
			<div id="ccm-search-field-base">				
				<table border="0" cellspacing="0" cellpadding="0">
					<tr>
						<td valign="top" style="padding-right: 4px">
						<?php echo $form->select('searchField', $searchFields, array('style' => 'width: 85px'));
						?>
						<input type="hidden" value="" class="ccm-<?php echo $searchInstance?>-selected-field" name="selectedSearchField[]" />
						</td>
						<td width="100%" valign="top" class="ccm-selected-field-content">
						<?php echo t('Select Search Field.')?>
						</td>
						<td valign="top">
						<a href="javascript:void(0)" class="ccm-search-remove-option"><img src="<?php echo ASSETS_URL_IMAGES?>/icons/remove_minus.png" width="16" height="16" /></a>
						</td>
					</tr>
				</table>
			</div>
			
			<div id="ccm-search-fields-wrapper">
			<?php  
			$i = 1;
			if (is_array($searchRequest['selectedSearchField'])) { 
				foreach($searchRequest['selectedSearchField'] as $req) { 
					
					if ($req == '') {
						continue;
					}
					
					?>
					
					<div class="ccm-search-field ccm-search-request-field-set" ccm-search-type="<?php echo $req?>" id="ccm-<?php echo $searchInstance?>-search-field-set<?php echo $i?>">
					<table border="0" cellspacing="0" cellpadding="0">
						<tr>
							<td valign="top" style="padding-right: 4px">
							<?php echo $form->select('searchField' . $i, $searchFields, $req, array('style' => 'width: 85px')); ?>
							<input type="hidden" value="<?php echo $req?>" class="ccm-<?php echo $searchInstance?>-selected-field" name="selectedSearchField[]" />
							</td>
							<td width="100%" valign="top" class="ccm-selected-field-content">
							<?php  if ($req == 'size') { ?>
								<span class="ccm-search-option" search-field="size">
								<?php echo $form->text('size_from', $searchRequest['size_from'], array('style' => 'width: 30px'))?>
								<?php echo t('to')?>
								<?php echo $form->text('size_to', $searchRequest['size_to'], array('style' => 'width: 30px'))?>
								KB
								</span>
							<?php  } ?>
							
							<?php  if ($req == 'type') { ?>
								<span class="ccm-search-option"  search-field="type">
								<?php echo $form->select('type', $types, $searchRequest['type'])?>
								</span>
							<?php  } ?>
							
							<?php  if ($req == 'extension') { ?>
								<span class="ccm-search-option"  search-field="extension">
								<?php echo $form->select('extension', $extensions, $searchRequest['extension'])?>
								</span>
							<?php  } ?>
							
							<?php  if ($req == 'date_added') { ?>
								<span class="ccm-search-option"  search-field="date_added">
								<?php echo $form->text('date_from', $searchRequest['date_from'], array('style' => 'width: 86px'))?>
								<?php echo t('to')?>
								<?php echo $form->text('date_to', $searchRequest['date_to'], array('style' => 'width: 86px'))?>
								</span>
							<?php  } ?>
							
							<?php  foreach($searchFieldAttributes as $sfa) { 
								if ($sfa->getAttributeKeyID() == $req) {
									$at = $sfa->getAttributeType();
									$at->controller->setRequestArray($searchRequest);
									$at->render('search', $sfa);
								}
							} ?>
							</td>
							<td valign="top">
							<a href="javascript:void(0)" class="ccm-search-remove-option"><img src="<?php echo ASSETS_URL_IMAGES?>/icons/remove_minus.png" width="16" height="16" /></a>
							</td>
						</tr>
					</table>
					</div>
					
				<?php  
					$i++;
				}
			}?>
			
			</div>
			
			<div id="ccm-search-fields-submit">
				<?php echo $form->submit('ccm-search-files', 'Search')?>
			</div>
		</div>
	
</div>

<?php  if (count($s1) > 0) { ?>

<div id="ccm-search-advanced-sets">
	<h2><?php echo t('Filter by File Set')?></h2>
	<div style="max-height: 200px; overflow: auto">
	<?php  foreach($s1 as $fs) { ?>
		<div class="ccm-<?php echo $searchInstance?>-search-advanced-sets-cb"><?php echo $form->checkbox('fsID[' . $fs->getFileSetID() . ']', $fs->getFileSetID(), (is_array($searchRequest['fsID']) && in_array($fs->getFileSetID(), $searchRequest['fsID'])))?> <?php echo $form->label('fsID[' . $fs->getFileSetID() . ']', $fs->getFileSetName())?></div>
	<?php  } ?>
	</div>
	
	<hr/>
	
	<div><?php echo $form->checkbox('fsIDNone', '1', $searchRequest['fsIDNone'] == 1, array('instance' => $searchInstance))?> <?php echo $form->label('fsIDNone', t('Display files in no sets.'))?></div>
</div>

<?php  } ?>
</form>	
