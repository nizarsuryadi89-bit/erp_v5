<?php
    defined('BASEPATH') or exit('No direct script access allowed');

    class Profile extends MY_Controller
    {
        public function __construct()
        {
            parent::__construct();
            date_default_timezone_set("Asia/Jakarta");
            $this->load->model('M_profile');
        }

        function myprofile(){
            $karyawan           = $this->session->userdata('karyawan_id');
            $conf  = array(
                'perusahaan'    => $this->db->get_where('tbl_perusahaan', array('id_perusahaan'=> $this->session->userdata('perusahaan_id')))->row_array(),
                'hakakses'      => $this->db->get_where('tbl_divisi', array('divisi_id'=> $this->session->userdata('level')))->row_array(),
            );

            $data = array(
                'title'             => 'My Profile',
                'logo'              => $conf['perusahaan']['logo'],
                'jam_lembur'        => $this->M_master->jml_jam_lembur()->row_array(),
                'profile'           => $this->M_profile->getprofile()->row_array(),
            );

            // print_r($data['profile']);

            $this->load->view('layout_admin/head', $data);
            $this->load->view('layout_admin/top_header');
            $this->load->view('layout_admin/sidebar', $data);
            $this->load->view('admin/profile/index', $data);
            $this->load->view('layout_admin/footer');
        }

        function aksi_edit_profile(){
            $id             = $this->input->post('karyawan_id', true);

            $where          = array(
                'karyawan_id' => $this->input->post('karyawan_id', true),
            );

            $data = [
                    'nama_lengkap'    => $this->input->post('nama_lengkap', true),
                    'jenis_kelamin'   => $this->input->post('jenis_kelamin', true),
                    'tempat_lahir'    => $this->input->post('tempat_lahir', true),
                    'tanggal_lahir'   => $this->input->post('tanggal_lahir', true),
                    'alamat'          => $this->input->post('alamat', true),
                    'telepon'         => $this->input->post('telepon', true),
                    'email'           => $this->input->post('email', true),
                ];

                // if (!empty($_FILES['foto']['name'])) {
                //     $config['upload_path']   = './assets/images/karyawan/';
                //     $config['allowed_types'] = 'jpg|jpeg|png';
                //     $config['max_size']      = 5048; // 2MB
                //     $config['file_name']     = 'karyawan_' . time();
                    
                //     $this->load->library('upload', $config);

                //     if ($this->upload->do_upload('foto')) {
                //         $upload_data = $this->upload->data();
                //         $data['foto'] = $upload_data['file_name'];

                //         // Hapus foto lama (jika ada)
                //         $old_foto = $this->M_master->get_by_id($id)->foto;
                //         if ($old_foto && file_exists('./assets/images/karyawan/' . $old_foto)) {
                //             unlink('./assets/images/karyawan/' . $old_foto);
                //         }
                //     } else {
                //         // Jika upload gagal
                //         $this->session->set_flashdata('error', $this->upload->display_errors());
                //         redirect('karyawan/edit-karyawan/' . $id);
                //     }
                // }

            $update = $this->M_master->updateData('tbl_karyawan', $where, $data);
            print_r($update);

            if ($update == true) {
                $this->session->set_flashdata('success', 'Data Berhasil Diupdate');
                redirect('profile/myprofile');
            }
        }

        public function ganti_password()
        {
            $user_id = $this->session->userdata('id');
            $user = $this->M_profile->get_by_id($user_id)->row_array();

            $password_lama = $this->input->post('password_lama', true);
            $password_baru = $this->input->post('password_baru', true);
            $konfirmasi    = $this->input->post('konfirmasi', true);

            print_r($password_baru);
            print_r($password_baru);

            // Verifikasi password lama
            if (!password_verify($password_lama, $user['password'])) {
                $this->session->set_flashdata('error', 'Password lama salah');
                redirect('mobile/reset_pass');
                return;
            }

            // Cek konfirmasi password
            if ($password_baru !== $konfirmasi) {
                $this->session->set_flashdata('error', 'Konfirmasi password tidak cocok');
                redirect('mobile/reset_pass');
                return;
            }

            // Hash password baru
            $hash_baru = password_hash($password_baru, PASSWORD_DEFAULT);

            // Update ke database
            $this->M_profile->update_password($user_id, $hash_baru);

            $this->session->set_flashdata('success', 'Password berhasil diubah');
            redirect('mobile');
        }



        
    }
