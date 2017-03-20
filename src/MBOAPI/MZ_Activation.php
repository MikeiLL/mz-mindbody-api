<?php
namespace mZoo\MBOAPI;

function MZ_Activation ()
{
  add_action('wp_loaded', 'initializeMBO');

  $data = $mb->GetActivationCode();
  $return = $mb->debug();

  return $return;

}//EOF MZ_Activation


function initializeMBO () {
			$mz_mbo = new MZ_MBO_Init();
		}