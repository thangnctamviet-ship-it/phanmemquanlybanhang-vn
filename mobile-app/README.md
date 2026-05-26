# QLBH Mobile App (Capacitor Android wrapper)

WebView wrapper cho `https://quanlybanhang.shop`.

## Yêu cầu
- Node.js >= 18
- **Android Studio** (cài Android SDK + platform-tools + build-tools 34)
- JDK 17 (Android Studio đi kèm)

Sau khi cài Android Studio, set biến môi trường:
```bash
export ANDROID_HOME="$HOME/Library/Android/sdk"
export PATH="$ANDROID_HOME/platform-tools:$ANDROID_HOME/cmdline-tools/latest/bin:$PATH"
```

## Build APK (lần đầu)
```bash
cd mobile-app
npm install
npx cap add android       # chỉ chạy 1 lần
npx cap sync android

# Copy icon (tùy chọn — Capacitor gen default; muốn icon đẹp dùng @capacitor/assets)
# npx @capacitor/assets generate --android --iconBackgroundColor "#4f46e5" --iconSource ../assets/pwa/icon-512.png

cd android
./gradlew assembleDebug      # ra app/build/outputs/apk/debug/app-debug.apk
# hoặc release (cần keystore):
# ./gradlew assembleRelease
```

## Tạo keystore self-signed (chỉ 1 lần, KHÔNG commit file .keystore)
```bash
keytool -genkey -v -keystore qlbh.keystore -alias qlbh -keyalg RSA -keysize 2048 -validity 10000 \
  -dname "CN=QuanLyBanHang,O=QuanLyBanHang,L=Hanoi,C=VN" -storepass qlbh1234 -keypass qlbh1234
```

Thêm vào `android/app/build.gradle`:
```gradle
android {
  signingConfigs {
    release {
      storeFile file("../../qlbh.keystore")
      storePassword "qlbh1234"
      keyAlias "qlbh"
      keyPassword "qlbh1234"
    }
  }
  buildTypes { release { signingConfig signingConfigs.release } }
}
```

## Upload APK lên hosting
```
scp app-debug.apk deploy@quanlybanhang.shop:/home/iqosvnsh/quanlybanhang.shop/downloads/quanlybanhang.apk
```
Hoặc qua FTP tới `/downloads/quanlybanhang.apk`.

## Cập nhật
App chỉ load URL từ xa nên KHÔNG cần build lại APK mỗi khi sửa web —
chỉ rebuild khi đổi `appId`, icon, permission, hoặc Capacitor plugin.
