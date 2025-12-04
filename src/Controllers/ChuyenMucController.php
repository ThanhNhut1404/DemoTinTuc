<?php
namespace Website\TinTuc\Controllers;

use Website\TinTuc\Models\BaiVietModel;
use Website\TinTuc\Models\ChuyenMucModel;
use Website\TinTuc\Models\ChuyenMucChaModel;

class ChuyenMucController
{
    // Backend: Quản lý danh mục
    public function index()
    {
        // Xử lý POST logic TRƯỚC khi include layout
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Parent category actions (separate table)
            if (isset($_POST['parent_action'])) {
                switch ($_POST['parent_action']) {
                    case 'add':
                        $this->handleAddParent();
                        break;
                    case 'update':
                        $this->handleUpdateParent();
                        break;
                    case 'delete':
                        $this->handleDeleteParent();
                        break;
                    case 'update_order':
                        $this->handleUpdateParentOrder();
                        break;
                }
            }

            // Child category actions (existing)
            if (isset($_POST['action'])) {
                switch ($_POST['action']) {
                    case 'add':
                        $this->handleAddCategory();
                        break;
                    case 'update':
                        $this->handleUpdateCategory();
                        break;
                    case 'delete':
                        $this->handleDeleteCategory();
                        break;
                    case 'update_order':
                        $this->handleUpdateOrder();
                        break;
                }
            }
        }
        
        // Sau khi xử lý POST, render layout
        $_GET['action'] = 'danh_muc';
        include __DIR__ . '/../../views/backend/layout.php';
    }

    private function handleAddCategory()
    {
        $chuyenMucModel = new ChuyenMucModel();
        $ten = trim($_POST['ten_chuyen_muc'] ?? '');
        $mo_ta = trim($_POST['mo_ta'] ?? '');
        $id_cha = $_POST['id_cha'] ?? null;
        
        if (empty($ten)) {
            $_SESSION['flash'] = "❌ Tên danh mục không được trống!";
        } else {
            try {
                $result = $chuyenMucModel->db->query("SELECT COALESCE(MAX(thu_tu), 0) + 1 as next_thu_tu FROM chuyen_muc");
                $row = $result->fetch(\PDO::FETCH_ASSOC);
                $next_thu_tu = $row['next_thu_tu'] ?? 1;
                
                $stmt = $chuyenMucModel->db->prepare("
                    INSERT INTO chuyen_muc (ten_chuyen_muc, mo_ta, id_cha, thu_tu) 
                    VALUES (?, ?, ?, ?)
                ");
                $stmt->execute([$ten, $mo_ta, $id_cha ?: null, $next_thu_tu]);
                $_SESSION['flash'] = "✅ Thêm danh mục thành công!";
                header('Location: admin.php?action=danh_muc&sub=danhsach');
                exit;
            } catch (\Exception $e) {
                $_SESSION['flash'] = "❌ Lỗi: " . $e->getMessage();
            }
        }
    }

    private function handleUpdateCategory()
    {
        $chuyenMucModel = new ChuyenMucModel();
        $id = $_POST['id'] ?? null;
        $ten = trim($_POST['ten_chuyen_muc'] ?? '');
        $mo_ta = trim($_POST['mo_ta'] ?? '');
        $id_cha = $_POST['id_cha'] ?? null;
        
        if (empty($id) || empty($ten)) {
            $_SESSION['flash'] = "❌ Dữ liệu không hợp lệ!";
        } else {
            try {
                $stmt = $chuyenMucModel->db->prepare("
                    UPDATE chuyen_muc SET ten_chuyen_muc = ?, mo_ta = ?, id_cha = ? WHERE id = ?
                ");
                $stmt->execute([$ten, $mo_ta, $id_cha ?: null, $id]);
                $_SESSION['flash'] = "✅ Cập nhật danh mục thành công!";
                header('Location: admin.php?action=danh_muc&sub=danhsach');
                exit;
            } catch (\Exception $e) {
                $_SESSION['flash'] = "❌ Lỗi: " . $e->getMessage();
            }
        }
    }

    private function handleDeleteCategory()
    {
        $chuyenMucModel = new ChuyenMucModel();
        $id = $_POST['id'] ?? null;
        
        if (empty($id)) {
            $_SESSION['flash'] = "❌ ID danh mục không hợp lệ!";
        } else {
            try {
                $stmt = $chuyenMucModel->db->prepare("DELETE FROM chuyen_muc WHERE id = ?");
                $stmt->execute([$id]);
                $_SESSION['flash'] = "✅ Xóa danh mục thành công!";
                header('Location: admin.php?action=danh_muc&sub=danhsach');
                exit;
            } catch (\Exception $e) {
                $_SESSION['flash'] = "❌ Lỗi: " . $e->getMessage();
            }
        }
    }

    private function handleUpdateOrder()
    {
        $chuyenMucModel = new ChuyenMucModel();
        $items = $_POST['items'] ?? [];
        
        try {
            foreach ($items as $index => $id) {
                $stmt = $chuyenMucModel->db->prepare("UPDATE chuyen_muc SET thu_tu = ? WHERE id = ?");
                $stmt->execute([$index + 1, $id]);
            }
            $_SESSION['flash'] = "✅ Cập nhật thứ tự thành công!";
        } catch (\Exception $e) {
            $_SESSION['flash'] = "❌ Lỗi: " . $e->getMessage();
        }
        header('Location: admin.php?action=danh_muc&sub=danhsach');
        exit;
    }

    // ===== Parent category handlers (chuyen_muc_cha) =====
    private function handleAddParent()
    {
        $parentModel = new ChuyenMucChaModel();
        $ten = trim($_POST['ten_chuyen_muc'] ?? '');
        $mo_ta = trim($_POST['mo_ta'] ?? '');

        if (empty($ten)) {
            $_SESSION['flash'] = "❌ Tên danh mục cha không được trống!";
        } else {
            try {
                $parentModel->add($ten, $mo_ta);
                $_SESSION['flash'] = "✅ Thêm danh mục cha thành công!";
                header('Location: admin.php?action=danh_muc&sub=parents');
                exit;
            } catch (\Exception $e) {
                $_SESSION['flash'] = "❌ Lỗi: " . $e->getMessage();
            }
        }
    }

    private function handleUpdateParent()
    {
        $parentModel = new ChuyenMucChaModel();
        $id = $_POST['id'] ?? null;
        $ten = trim($_POST['ten_chuyen_muc'] ?? '');
        $mo_ta = trim($_POST['mo_ta'] ?? '');

        if (empty($id) || empty($ten)) {
            $_SESSION['flash'] = "❌ Dữ liệu không hợp lệ!";
        } else {
            try {
                $parentModel->update($id, $ten, $mo_ta);
                $_SESSION['flash'] = "✅ Cập nhật danh mục cha thành công!";
                header('Location: admin.php?action=danh_muc&sub=parents');
                exit;
            } catch (\Exception $e) {
                $_SESSION['flash'] = "❌ Lỗi: " . $e->getMessage();
            }
        }
    }

    private function handleDeleteParent()
    {
        $parentModel = new ChuyenMucChaModel();
        $id = $_POST['id'] ?? null;

        if (empty($id)) {
            $_SESSION['flash'] = "❌ ID không hợp lệ!";
        } else {
            try {
                $parentModel->delete($id);
                $_SESSION['flash'] = "✅ Xóa danh mục cha thành công!";
                header('Location: admin.php?action=danh_muc&sub=parents');
                exit;
            } catch (\Exception $e) {
                $_SESSION['flash'] = "❌ Lỗi: " . $e->getMessage();
            }
        }
    }

    private function handleUpdateParentOrder()
    {
        $parentModel = new ChuyenMucChaModel();
        $items = $_POST['items'] ?? [];

        try {
            foreach ($items as $index => $id) {
                $parentModel->db->prepare("UPDATE chuyen_muc_cha SET thu_tu = ? WHERE id = ?")->execute([$index + 1, $id]);
            }
            $_SESSION['flash'] = "✅ Cập nhật thứ tự danh mục cha thành công!";
        } catch (\Exception $e) {
            $_SESSION['flash'] = "❌ Lỗi: " . $e->getMessage();
        }
        header('Location: admin.php?action=danh_muc&sub=parents');
        exit;
    }

    // Frontend: Hiển thị bài viết theo chuyên mục
    public function hienThiTheoChuyenMuc($id)
    {
        if (!$id || !is_numeric($id)) {
            die("❌ ID chuyên mục không hợp lệ.");
        }

        $baiVietModel = new BaiVietModel();
        $chuyenMucModel = new ChuyenMucModel();

        // ✅ Lấy thông tin chuyên mục
        $chuyenMuc = $chuyenMucModel->getById($id);
        if (!$chuyenMuc) {
            die("❌ Không tìm thấy chuyên mục này.");
        }

        $tenChuyenMuc = $chuyenMuc['ten_chuyen_muc'];

        // ✅ Bộ lọc (mặc định là 'moi_nhat')
        $filter = $_GET['filter'] ?? 'moi_nhat';

        // ✅ Phân trang
        $page = $_GET['page'] ?? 1;
        $limit = 10;
        $offset = ($page - 1) * $limit;

        // ✅ Lấy danh sách bài viết có áp dụng bộ lọc
        $baiViet = $baiVietModel->getByChuyenMucFilter($id, $limit, $offset, $filter);

        // ✅ Tổng số bài viết để tính phân trang
        $total = $baiVietModel->countByChuyenMuc($id);
        $totalPages = ceil($total / $limit);

        // ✅ Load view
        include __DIR__ . '/../../views/frontend/chuyen_muc.php';
    }

    // Frontend: Hiển thị tất cả chuyên mục con của chuyên mục cha
    public function hienThiChuyenMucCha($id)
    {
        if (!$id || !is_numeric($id)) {
            die("❌ ID chuyên mục cha không hợp lệ.");
        }

        $chuyenMucChaModel = new ChuyenMucChaModel();
        $chuyenMucModel = new ChuyenMucModel();

        // ✅ Lấy thông tin chuyên mục cha
        $chuyenMucCha = $chuyenMucChaModel->getById($id);
        if (!$chuyenMucCha) {
            die("❌ Không tìm thấy chuyên mục cha này.");
        }

        $tenChuyenMucCha = $chuyenMucCha['ten_chuyen_muc'];

        // ✅ Lấy tất cả chuyên mục con của chuyên mục cha này
        $dsChuyenMuc = $chuyenMucModel->getAll();
        $chuyenMucCon = [];
        foreach ($dsChuyenMuc as $cm) {
            if (($cm['id_cha'] ?? null) == $id) {
                $chuyenMucCon[] = $cm;
            }
        }

        // ✅ Load view
        include __DIR__ . '/../../views/frontend/chuyen_muc_cha.php';
    }
}
