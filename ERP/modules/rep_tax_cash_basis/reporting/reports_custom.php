<?php
// ----------------------------------------------------------------
// $ Revision:  1.0 $
// Creator: Alastair Robertson
// date_:   2011-10-22
// Title:   Report hook for tax details (cash basis)
// Free software under GNU GPL
// ----------------------------------------------------------------

global $reports, $dim;

$reports->addReport(RC_GL, "_tax_details_cash", trans('Tax Details (Cash Basis)'),
	array(	trans('Start Date') => 'DATEBEGINTAX',
			trans('End Date') => 'DATEENDTAX',
            trans('Net Output/Input Amounts') => 'YES_NO',
			trans('Comments') => 'TEXTBOX',
			trans('Destination') => 'DESTINATION'));
?>
