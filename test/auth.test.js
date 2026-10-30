const request = require("supertest"); // Nạp thư viện Supertest để gửi các HTTP request đến ứng dụng Express và kiểm tra phản hồi mà không cần khởi chạy máy chủ riêng.
const app = require("../app"); // Nạp ứng dụng Express từ file app.js nằm ở thư mục cha để thực hiện kiểm thử các API đã định nghĩa.

describe("API Đăng ký / Đăng nhập", () => { // Tạo một nhóm kiểm thử có tên API Đăng ký / Đăng nhập bằng hàm describe() của Jest để gom các test liên quan vào cùng một nhóm.
  const username = // Khai báo biến username để sử dụng chung cho các trường hợp kiểm thử đăng ký và đăng nhập.
    `student_${Date.now()}_${Math.random().toString(36).slice(2, 8)}`; // Tạo tên đăng nhập gần như duy nhất bằng cách ghép chữ student, thời điểm hiện tại tính bằng mili giây và một chuỗi ngẫu nhiên để tránh trùng tài khoản giữa các lần chạy test.

  const password = "Student@123"; // Khai báo mật khẩu dùng chung cho các test đăng ký và đăng nhập; mật khẩu có 11 ký tự và đáp ứng yêu cầu tối thiểu 8 ký tự của service.

  test("Đăng ký tài khoản thành công", async () => { // Tạo test đầu tiên để kiểm tra trường hợp người dùng đăng ký tài khoản hợp lệ; async cho phép sử dụng await.
    const response = await request(app) // Tạo một đối tượng request đến ứng dụng Express và chờ kết quả HTTP trả về được lưu vào biến response.
      .post("/api/auth/register") // Gửi yêu cầu HTTP POST đến đường dẫn API đăng ký tài khoản.
      .send({ username, password }); // Gửi username và password trong phần body dưới dạng JSON; tương đương { username: username, password: password }.

    expect(response.status).toBe(201); // Kiểm tra mã trạng thái HTTP trả về là 201 Created, nghĩa là đăng ký thành công và tài khoản mới đã được tạo.
    expect(response.body.user.username).toBe(username); // Kiểm tra tên đăng nhập trong dữ liệu JSON phản hồi phải bằng username đã gửi lên API.
    expect(response.body.user.passwordHash).toBeUndefined(); // Kiểm tra đối tượng user trả về không chứa thuộc tính passwordHash, nhằm xác nhận API không tiết lộ chuỗi băm mật khẩu trong phản hồi.
  }); // Kết thúc test kiểm tra đăng ký thành công.

  test("Không cho đăng ký trùng tên", async () => { // Tạo test kiểm tra API có từ chối đăng ký khi tên đăng nhập đã tồn tại hay không.
    const response = await request(app) // Gửi request đến ứng dụng và chờ phản hồi từ API.
      .post("/api/auth/register") // Gửi yêu cầu POST đến API đăng ký.
      .send({ username, password }); // Gửi lại chính username và password đã sử dụng ở test đăng ký thành công; vì test trước đã tạo tài khoản này nên lần đăng ký này phải bị từ chối.

    expect(response.status).toBe(409); // Kiểm tra API trả về HTTP 409 Conflict, thể hiện tên đăng nhập bị trùng; test này phụ thuộc vào việc test đăng ký thành công trước đó đã chạy và tạo tài khoản.
  }); // Kết thúc test kiểm tra đăng ký trùng tên.

  test("Đăng nhập thành công", async () => { // Tạo test kiểm tra người dùng có thể đăng nhập bằng thông tin tài khoản hợp lệ đã đăng ký hay không.
    const response = await request(app) // Tạo request đến ứng dụng và chờ kết quả phản hồi.
      .post("/api/auth/login") // Gửi yêu cầu POST đến API đăng nhập.
      .send({ username, password }); // Gửi tên đăng nhập và mật khẩu chính xác đã sử dụng khi đăng ký.

    expect(response.status).toBe(200); // Kiểm tra API trả về HTTP 200 OK, nghĩa là đăng nhập thành công.
    expect(response.body.message).toBe("Đăng nhập thành công"); // Kiểm tra nội dung thông báo trong JSON phản hồi đúng bằng chuỗi Đăng nhập thành công.
  }); // Kết thúc test đăng nhập thành công.

  test("Từ chối mật khẩu sai", async () => { // Tạo test kiểm tra API có từ chối đăng nhập khi mật khẩu không chính xác hay không.
    const response = await request(app) // Tạo request đến ứng dụng và chờ phản hồi.
      .post("/api/auth/login") // Gửi yêu cầu POST đến API đăng nhập.
      .send({ // Bắt đầu đối tượng JSON chứa dữ liệu gửi đến API.
        username, // Gửi tên đăng nhập hợp lệ đã đăng ký trước đó.
        password: "WrongPass123" // Gửi mật khẩu sai để kiểm tra khả năng phát hiện thông tin xác thực không chính xác.
      }); // Kết thúc đối tượng dữ liệu gửi lên API.

    expect(response.status).toBe(401); // Kiểm tra API trả về HTTP 401 Unauthorized vì tên đăng nhập hoặc mật khẩu không đúng.
  }); // Kết thúc test từ chối mật khẩu sai.

  test("Từ chối mật khẩu quá ngắn", async () => { // Tạo test kiểm tra API có từ chối đăng ký khi mật khẩu không đáp ứng yêu cầu độ dài tối thiểu hay không.
    const response = await request(app) // Gửi request đến ứng dụng và chờ phản hồi.
      .post("/api/auth/register") // Gửi yêu cầu POST đến API đăng ký tài khoản.
      .send({ // Bắt đầu đối tượng JSON chứa dữ liệu đăng ký.
        username: `short_${Date.now()}`, // Tạo tên đăng nhập mới bằng cách ghép short_ với thời gian hiện tại để hạn chế khả năng trùng tên.
        password: "123" // Gửi mật khẩu chỉ có 3 ký tự, ngắn hơn yêu cầu tối thiểu 8 ký tự.
      }); // Kết thúc dữ liệu gửi lên API.

    expect(response.status).toBe(400); // Kiểm tra API trả về HTTP 400 Bad Request vì mật khẩu không hợp lệ.
  }); // Kết thúc test từ chối mật khẩu quá ngắn.

  test("Từ chối tên đăng nhập chỉ chứa khoảng trắng", async () => { // Tạo test kiểm tra API có từ chối username không chứa ký tự thực sự mà chỉ có khoảng trắng hay không.
    const response = await request(app) // Gửi request đến ứng dụng và chờ phản hồi.
      .post("/api/auth/register") // Gửi yêu cầu POST đến API đăng ký.
      .send({ // Bắt đầu đối tượng JSON chứa dữ liệu đăng ký.
        username: "   ", // Gửi tên đăng nhập gồm ba dấu cách; sau khi gọi trim(), chuỗi này sẽ trở thành chuỗi rỗng.
        password // Gửi mật khẩu hợp lệ đã khai báo phía trên; tương đương password: password.
      }); // Kết thúc đối tượng dữ liệu gửi lên API.

    expect(response.status).toBe(400); // Kiểm tra API trả về HTTP 400 Bad Request vì tên đăng nhập không hợp lệ.
  }); // Kết thúc test từ chối tên đăng nhập chỉ có khoảng trắng.

  test("Từ chối khi thiếu mật khẩu", async () => { // Tạo test kiểm tra API có từ chối đăng ký khi người dùng không gửi trường password hay không.
    const response = await request(app) // Gửi request đến ứng dụng và chờ phản hồi.
      .post("/api/auth/register") // Gửi yêu cầu POST đến API đăng ký tài khoản.
      .send({ // Bắt đầu đối tượng JSON gửi đến API.
        username: `missing_${Date.now()}` // Gửi một tên đăng nhập mới nhưng cố ý không gửi thuộc tính password để kiểm tra trường hợp thiếu dữ liệu bắt buộc.
      }); // Kết thúc đối tượng dữ liệu gửi lên API.

    expect(response.status).toBe(400); // Kiểm tra API trả về HTTP 400 Bad Request vì thiếu mật khẩu.
  }); // Kết thúc test từ chối đăng ký khi thiếu mật khẩu.
}); // Kết thúc nhóm kiểm thử API Đăng ký / Đăng nhập.
