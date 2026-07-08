import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../core/network/dio_client.dart';
import '../../core/storage/secure_storage.dart';

// -- Storage --
final secureStorageProvider = Provider<SecureStorage>((ref) => SecureStorage());

// -- Dio Client --
final dioClientProvider = Provider<DioClient>(
  (ref) {
    final storage = ref.watch(secureStorageProvider);
    return DioClient(secureStorage: storage);
  },
);

// -- Auth State --
final authStateProvider = StateProvider<bool>((ref) => false);
