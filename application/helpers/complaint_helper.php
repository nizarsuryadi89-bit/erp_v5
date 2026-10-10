<?php
defined('BASEPATH') OR exit('No direct script access allowed');

function complaint_e($value) { return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }

function complaint_badge($value)
{
    $colors = array('OPEN' => 'secondary', 'ASSIGNED' => 'info', 'ON_PROGRESS' => 'primary', 'RESOLVED' => 'success',
        'CLOSED' => 'dark', 'LOW' => 'secondary', 'MEDIUM' => 'info', 'HIGH' => 'warning', 'CRITICAL' => 'danger');
    $color = isset($colors[$value]) ? $colors[$value] : 'secondary';
    return '<span class="badge text-bg-'.$color.'">'.complaint_e($value).'</span>';
}

function complaint_form_token($module_token)
{
    $ci = get_instance();
    echo '<input type="hidden" name="complaint_csrf" value="'.complaint_e($module_token).'">';
    if ($ci->config->item('csrf_protection')) {
        echo '<input type="hidden" name="'.complaint_e($ci->security->get_csrf_token_name()).'" value="'.complaint_e($ci->security->get_csrf_hash()).'">';
    }
}

function complaint_pagination($path, $data, $filters = array())
{
    echo '<nav class="d-flex justify-content-between align-items-center mt-3" aria-label="Halaman">';
    echo '<span class="text-muted small">'.(int) $data['total'].' data · Halaman '.(int) $data['page'].' / '.(int) $data['pages'].'</span><div class="btn-group">';
    foreach (array(-1 => 'Sebelumnya', 1 => 'Berikutnya') as $delta => $label) {
        $target = $data['page'] + $delta;
        if ($target >= 1 && $target <= $data['pages']) {
            $query = array_merge($filters, array('page' => $target));
            echo '<a class="btn btn-outline-secondary btn-sm" href="'.complaint_e(site_url($path).'?'.http_build_query($query)).'">'.$label.'</a>';
        }
    }
    echo '</div></nav>';
}
