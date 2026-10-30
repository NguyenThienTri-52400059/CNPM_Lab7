const bcrypt = require("bcryptjs"); // Nạp thư viện bcryptjs để băm mật khẩu và kiểm tra mật khẩu khi đăng nhập, tránh lưu mật khẩu dạng văn bản thuần.

const users = []; // Tạo mảng users để lưu thông tin người dùng tạm thời trong bộ nhớ; khi khởi động lại ứng dụng, dữ liệu trong mảng sẽ mất.
let nextId = 1; // Khởi tạo ID của người dùng tiếp theo bằng 1; mỗi lần đăng ký thành công sẽ tăng ID lên 1.

async function register(username, password) { // Khai báo hàm bất đồng bộ register nhận tên đăng nhập username và mật khẩu password để đăng ký tài khoản.
  if ( // Bắt đầu kiểm tra dữ liệu đầu vào có đúng kiểu dữ liệu mong muốn hay không.
    typeof username !== "string" || // Kiểm tra username có phải kiểu chuỗi hay không; nếu không phải chuỗi thì điều kiện đúng.
    typeof password !== "string" // Kiểm tra password có phải kiểu chuỗi hay không; toán tử || nghĩa là chỉ cần một điều kiện đúng thì toàn bộ điều kiện đúng.
  ) { // Bắt đầu khối xử lý khi dữ liệu đầu vào không hợp lệ.
    throw new Error("Tên đăng nhập và mật khẩu phải là chuỗi"); // Ném ra lỗi để dừng đăng ký và chuyển quyền xử lý lỗi cho nơi gọi hàm.
  } // Kết thúc khối kiểm tra kiểu dữ liệu.

  const normalizedUsername = username.trim().toLowerCase(); // Xóa khoảng trắng ở đầu và cuối tên đăng nhập bằng trim(), sau đó chuyển thành chữ thường bằng toLowerCase() để chuẩn hóa tên tài khoản.

  if (!normalizedUsername || !password) { // Kiểm tra tên đăng nhập sau chuẩn hóa có rỗng hoặc mật khẩu có rỗng hay không; dấu ! biểu thị phủ định.
    throw new Error("Tên đăng nhập và mật khẩu là bắt buộc"); // Báo lỗi nếu người dùng chưa nhập tên đăng nhập hoặc mật khẩu.
  } // Kết thúc khối kiểm tra dữ liệu bắt buộc.

  if (password.length < 8) { // Kiểm tra độ dài mật khẩu; length trả về số ký tự và điều kiện này yêu cầu tối thiểu 8 ký tự.
    throw new Error("Mật khẩu phải có ít nhất 8 ký tự"); // Báo lỗi nếu mật khẩu có ít hơn 8 ký tự.
  } // Kết thúc kiểm tra độ dài mật khẩu.

  const existed = users.some( // Dùng some() để kiểm tra xem có ít nhất một người dùng trong mảng users thỏa mãn điều kiện hay không.
    user => user.username === normalizedUsername // Với từng user trong mảng, so sánh username đã lưu với tên đăng nhập vừa chuẩn hóa; nếu trùng thì trả về true.
  ); // Kết thúc some() và lưu kết quả true hoặc false vào biến existed.

  if (existed) { // Kiểm tra biến existed; nếu true nghĩa là tên đăng nhập đã được sử dụng.
    throw new Error("Tên đăng nhập đã tồn tại"); // Báo lỗi để ngăn việc đăng ký tài khoản trùng tên.
  } // Kết thúc kiểm tra tên đăng nhập trùng.

  const passwordHash = await bcrypt.hash(password, 10); // Băm mật khẩu bằng bcrypt với cost factor 10; await chờ quá trình băm hoàn thành rồi lưu chuỗi hash vào passwordHash.

  const user = { // Tạo đối tượng user chứa các thông tin cần lưu của tài khoản mới.
    id: nextId++, // Gán ID hiện tại cho user rồi tăng nextId lên 1; ví dụ user đầu tiên nhận ID 1, lần tiếp theo nhận ID 2.
    username: normalizedUsername, // Lưu tên đăng nhập đã chuẩn hóa để tránh khác biệt do chữ hoa, chữ thường hoặc khoảng trắng ở hai đầu.
    passwordHash // Lưu mật khẩu đã băm, tương đương passwordHash: passwordHash; không lưu mật khẩu gốc vào thuộc tính này.
  }; // Kết thúc việc tạo đối tượng user.

  users.push(user); // Thêm đối tượng user vừa tạo vào cuối mảng users để lưu tài khoản trong bộ nhớ.

  return { // Trả về một đối tượng chứa thông tin tài khoản được phép gửi cho nơi gọi hàm.
    id: user.id, // Trả về ID của người dùng.
    username: user.username // Trả về tên đăng nhập; không trả passwordHash để hạn chế lộ thông tin xác thực.
  }; // Kết thúc đối tượng kết quả đăng ký.
} // Kết thúc hàm register.

async function login(username, password) { // Khai báo hàm bất đồng bộ login nhận tên đăng nhập và mật khẩu để xác thực người dùng.
  if ( // Bắt đầu kiểm tra tính hợp lệ của dữ liệu đăng nhập.
    typeof username !== "string" || // Kiểm tra username có phải chuỗi hay không.
    typeof password !== "string" || // Kiểm tra password có phải chuỗi hay không.
    !username.trim() || // Kiểm tra username sau khi xóa khoảng trắng có còn nội dung hay không; nếu chỉ có khoảng trắng thì điều kiện đúng.
    !password // Kiểm tra password có phải chuỗi rỗng hay không.
  ) { // Bắt đầu khối xử lý khi dữ liệu đăng nhập không hợp lệ.
    throw new Error("Tên đăng nhập và mật khẩu là bắt buộc"); // Ném lỗi nếu thiếu tên đăng nhập, thiếu mật khẩu hoặc dữ liệu không đúng kiểu.
  } // Kết thúc khối kiểm tra dữ liệu đăng nhập.

  const normalizedUsername = username.trim().toLowerCase(); // Chuẩn hóa tên đăng nhập giống như lúc đăng ký để việc tìm kiếm không bị ảnh hưởng bởi chữ hoa, chữ thường hoặc khoảng trắng ở hai đầu.

  const user = users.find( // Dùng find() để tìm và trả về người dùng đầu tiên thỏa mãn điều kiện trong mảng users; nếu không tìm thấy thì trả về undefined.
    item => item.username === normalizedUsername // So sánh tên đăng nhập của từng tài khoản với tên đăng nhập đã chuẩn hóa.
  ); // Kết thúc find() và lưu người dùng tìm được vào biến user.

  if ( // Bắt đầu kiểm tra tài khoản có tồn tại và mật khẩu có chính xác hay không.
    !user || // Nếu không tìm thấy tài khoản, user là undefined và !user sẽ có giá trị true.
    !(await bcrypt.compare(password, user.passwordHash)) // Nếu tài khoản tồn tại, dùng bcrypt.compare() so sánh mật khẩu người dùng nhập với mật khẩu đã băm; nếu không khớp thì kết quả compare là false và dấu ! biến thành true.
  ) { // Nếu không tìm thấy tài khoản hoặc mật khẩu không đúng thì thực hiện khối này.
    throw new Error("Tên đăng nhập hoặc mật khẩu không đúng"); // Báo lỗi đăng nhập thất bại; dùng chung thông báo để tránh tiết lộ tài khoản có tồn tại hay không.
  } // Kết thúc kiểm tra thông tin xác thực.

  return { // Nếu vượt qua các điều kiện trên, trả về thông tin cơ bản của tài khoản đã đăng nhập.
    id: user.id, // Trả về ID người dùng.
    username: user.username // Trả về tên đăng nhập; không trả mật khẩu hoặc passwordHash.
  }; // Kết thúc đối tượng kết quả đăng nhập.
} // Kết thúc hàm login.

module.exports = { register, login }; // Xuất hai hàm register và login để các file khác, chẳng hạn controller hoặc app.js, có thể require() và sử dụng.
