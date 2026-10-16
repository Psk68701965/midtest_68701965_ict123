<template>
  <div class="container mt-4">
    <h2 class="mb-3">รายชื่อนักศึกษา</h2>

    <div class="mb-3">
      <button class="btn btn-primary" @click="openAddModal">
        Add <i class="bi bi-plus-circle"></i>
      </button>
    </div>

    <table class="table table-bordered table-striped">
      <thead class="table-primary">
        <tr>
          <th>รหัสนักศึกษา</th>
          <th>ชื่อ</th>
          <th>นามสกุล</th>
          <th>เบอร์โทร</th>
          <th>อีเมล</th>
          <th>แก้ไข/ลบ</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="(item) in students" :key="item.student_id">
          <td>{{ item.student_id }}</td>
          <td>{{ item.first_name }}</td>
          <td>{{ item.last_name }}</td>
          <td>{{ item.phone }}</td>
          <td>{{ item.email }}</td>
          <td>
            <button class="btn btn-warning btn-sm" @click="openEditModal(item)">
              แก้ไข
            </button>
            |
            <button class="btn btn-danger btn-sm" @click="deleteStudent(item.student_id)">
              ลบ
            </button>
          </td>
        </tr>
      </tbody>
    </table>

    <div v-if="!loading && students.length === 0" class="alert alert-info">
      ไม่มีข้อมูลนักศึกษา
    </div>

    <div v-if="loading" class="text-center"><p>กำลังโหลดข้อมูล...</p></div>
    <div v-if="error" class="alert alert-danger">{{ error }}</div>

    <div class="modal fade" id="editModal" tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">{{ isEditMode ? "แก้ไขข้อมูลนักศึกษา" : "เพิ่มนักศึกษาใหม่" }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <form @submit.prevent="saveStudent">
              <div class="mb-3">
                <label class="form-label">รหัสนักศึกษา</label>
                <input v-model="editStudent.student_id" type="text" class="form-control" required>
              </div>
              <div class="mb-3">
                <label class="form-label">ชื่อ</label>
                <input v-model="editStudent.first_name" type="text" class="form-control" required>
              </div>
              <div class="mb-3">
                <label class="form-label">นามสกุล</label>
                <input v-model="editStudent.last_name" type="text" class="form-control" required>
              </div>
              <div class="mb-3">
                <label class="form-label">เบอร์โทร</label>
                <input v-model="editStudent.phone" class="text" required></input>
              </div>
              <div class="mb-3">
                <label class="form-label">อีเมล</label>
                <input v-model="editStudent.email" type="email" class="form-control" required>
              </div>
              <button type="submit" class="btn btn-success">
                {{ isEditMode ? "บันทึกการแก้ไข" : "เพิ่มนักศึกษา" }}
              </button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted } from "vue";

export default {
  name: "StudentList",
  setup() {
    const students = ref([]);
    const loading = ref(true);
    const error = ref(null);
    const editStudent = ref({});
    const isEditMode = ref(false);
    const originalStudentId = ref(null);
    let editModal = null;

    const fetchStudents = async () => {
      try {
        const response = await fetch("http://localhost/midtest_68701965_ict123/php_api/student_crud.php?action=read");

        if (!response.ok) {
          throw new Error(`โหลดข้อมูลนักศึกษาไม่สำเร็จ (HTTP ${response.status})`);
        }

        const result = await response.json();

        if (result.success) {
          students.value = result.data;
        } else {
          error.value = result.message;
        }
      } catch (err) {
        error.value = err.message;
      } finally {
        loading.value = false;
      }
    };

    onMounted(() => {
      fetchStudents();
      const modalEl = document.getElementById("editModal");
      editModal = new window.bootstrap.Modal(modalEl);
    });

    const openAddModal = () => {
      isEditMode.value = false;
      originalStudentId.value = null;
      editStudent.value = {
        student_id: "",
        first_name: "",
        last_name: "",
        phone: "",
        email: ""
      };
      editModal.show();
    };

    const openEditModal = (student) => {
      isEditMode.value = true;
      originalStudentId.value = student.student_id;
      editStudent.value = { ...student };
      editModal.show();
    };

    const saveStudent = async () => {
      const url = "http://localhost/midtest_68701965_ict123/php_api/student_crud.php";
      const method = isEditMode.value ? "PUT" : "POST";

      try {
        const response = await fetch(url, {
          method,
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({
            ...editStudent.value,
            current_student_id: originalStudentId.value
          })
        });

        const result = await response.json();

        if (result.success) {
          alert(result.message);
          fetchStudents();
          editModal.hide();
        } else {
          alert(result.message);
        }
      } catch (err) {
        alert("เกิดข้อผิดพลาด: " + err.message);
      }
    };

    const deleteStudent = async (student_id) => {
      if (!confirm("คุณต้องการลบข้อมูลนี้ใช่หรือไม่?")) return;
      try {
        const response = await fetch("http://localhost/midtest_68701965_ict123/php_api/student_crud.php", {
          method: "DELETE",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({ student_id: student_id })
        });

        if (!response.ok) {
          throw new Error(`ลบข้อมูลนักศึกษาไม่สำเร็จ (HTTP ${response.status})`);
        }

        const result = await response.json();
        if (result.success) {
          students.value = students.value.filter(c => c.student_id !== student_id);
          alert(result.message);
        } else {
          alert(result.message);
        }
      } catch (err) {
        alert("เกิดข้อผิดพลาด: " + err.message);
      }
    };

    return {
      students,
      loading,
      error,
      editStudent,
      isEditMode,
      openAddModal,
      openEditModal,
      saveStudent,
      deleteStudent
    };
  }
};
</script>
