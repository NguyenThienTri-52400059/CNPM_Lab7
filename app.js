const express = require("express"); // Nạp thư viện Express để xây dựng máy chủ web và định nghĩa các API.
const authService = require("./service/authService"); // Nạp module authService chứa các hàm xử lý nghiệp vụ đăng ký và đăng nhập.

const app = express(); // Tạo một ứng dụng Express để cấu hình các API và xử lý yêu cầu từ client.

app.use(express.json()); // Đăng ký middleware giúp Express đọc dữ liệu JSON trong phần body của request và chuyển thành đối tượng JavaScript req.body.

app.post("/api/auth/register", async (req, res) => { // Khai báo API POST /api/auth/register để tiếp nhận yêu cầu đăng ký tài khoản; async cho phép sử dụng await bên trong hàm.
  try { // Bắt đầu khối try để thực hiện đăng ký và xử lý các lỗi phát sinh.
    const { username, password } = req.body || {}; // Lấy username và password từ body; nếu req.body không có dữ liệu thì dùng đối tượng rỗng để tránh lỗi khi phân rã.
    const user = await authService.register(username, password); // Gọi hàm đăng ký trong authService và chờ kết quả trả về; await thường được dùng khi hàm xử lý bất đồng bộ.
    return res.status(201).json({ // Trả HTTP 201 Created khi đăng ký thành công và gửi dữ liệu phản hồi dưới dạng JSON.
      message: "Đăng ký thành công", // Tạo thuộc tính message để thông báo đăng ký thành công cho client.
      user // Đưa thông tin người dùng do authService trả về vào phản hồi JSON; tương đương user: user.
    }); // Kết thúc đối tượng JSON và gửi phản hồi về client.
  } catch (error) { // Bắt lỗi được ném ra từ authService.register hoặc lỗi phát sinh trong khối try.
    const status = // Khai báo biến status để xác định mã trạng thái HTTP dựa trên loại lỗi.
      error.message === "Tên đăng nhập đã tồn tại" // Kiểm tra thông báo lỗi có phải là tên đăng nhập đã tồn tại hay không.
        ? 409 // Nếu đúng, trả mã HTTP 409 Conflict vì dữ liệu đăng ký bị xung đột.
        : 400; // Nếu không phải lỗi trùng tên, trả mã HTTP 400 Bad Request.
    return res.status(status).json({ // Trả về mã trạng thái đã xác định và dữ liệu JSON chứa thông báo lỗi.
      message: error.message // Gửi nội dung lỗi để client biết nguyên nhân đăng ký thất bại.
    }); // Kết thúc đối tượng JSON phản hồi lỗi.
  } // Kết thúc khối xử lý đăng ký.
}); // Kết thúc định nghĩa API đăng ký.

/* API đăng nhập tài khoản */
app.post("/api/auth/login", async (req, res) => { // Khai báo API POST /api/auth/login để tiếp nhận thông tin đăng nhập.
  try { // Bắt đầu khối try để xử lý yêu cầu đăng nhập.
    const { username, password } = req.body || {}; // Lấy tên đăng nhập và mật khẩu từ phần body của request.
    const user = await authService.login(username, password); // Gọi hàm login trong authService và chờ kết quả xác thực tài khoản.
    return res.status(200).json({ // Nếu đăng nhập thành công, trả mã HTTP 200 OK và dữ liệu JSON.
      message: "Đăng nhập thành công", // Thông báo đăng nhập thành công cho client.
      user // Đính kèm thông tin người dùng đã đăng nhập vào phản hồi.
    }); // Kết thúc phản hồi JSON thành công.
  } catch (error) { // Bắt lỗi nếu xác thực thất bại hoặc xảy ra lỗi trong quá trình xử lý.
    const status = // Khai báo biến status để lựa chọn mã HTTP phù hợp với nguyên nhân đăng nhập thất bại.
      error.message === // Bắt đầu so sánh nội dung thông báo lỗi.
      "Tên đăng nhập hoặc mật khẩu không đúng" // Kiểm tra lỗi có phải do thông tin đăng nhập không chính xác hay không.
        ? 401 // Nếu đúng, trả HTTP 401 Unauthorized vì người dùng chưa được xác thực.
        : 400; // Nếu là lỗi đầu vào khác, trả HTTP 400 Bad Request.
    return res.status(status).json({ // Gửi mã trạng thái HTTP và nội dung JSON về cho client.
      message: error.message // Đưa thông báo lỗi vào phản hồi để client biết lý do đăng nhập thất bại.
    }); // Kết thúc phản hồi JSON lỗi.
  } // Kết thúc khối xử lý đăng nhập.
}); // Kết thúc định nghĩa API đăng nhập.

module.exports = app; // Xuất đối tượng app để file khác có thể nạp ứng dụng, chẳng hạn file server.js hoặc các file kiểm thử Jest.
