<?php

	$result = array(
		array('tid'=>"4",'tProduct'=>"105.1", 'tCE'=>"24", 'tAbundance'=>"20036", 'tRIInt'=>"100" ),
		array('tid'=>"4",'tProduct'=>"105.1", 'tCE'=>"24", 'tAbundance'=>"20036", 'tRIInt'=>"100" ),
		array('tid'=>"4",'tProduct'=>"105.1", 'tCE'=>"24", 'tAbundance'=>"20036", 'tRIInt'=>"100" ),
		array('tid'=>"4",'tProduct'=>"105.1", 'tCE'=>"24", 'tAbundance'=>"20036", 'tRIInt'=>"100" ),
		array('tid'=>"4",'tProduct'=>"105.1", 'tCE'=>"24", 'tAbundance'=>"20036", 'tRIInt'=>"100" ),
		array('tid'=>"4",'tProduct'=>"105.1", 'tCE'=>"24", 'tAbundance'=>"20036", 'tRIInt'=>"100" ),
		array('tid'=>"4",'tProduct'=>"105.1", 'tCE'=>"24", 'tAbundance'=>"20036", 'tRIInt'=>"100" )
	);

	$return_values = json_encode($result);
	header('Content-Type: application/json');
	echo $return_values;
?>