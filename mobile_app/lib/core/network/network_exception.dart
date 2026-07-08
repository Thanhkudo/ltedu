class NetworkException implements Exception {
  const NetworkException(this.message, {this.statusCode});

  final String message;
  final int? statusCode;

  factory NetworkException.fromDioError(Object error) {
    if (error is NetworkException) return error;
    return NetworkException(error.toString());
  }

  @override
  String toString() => message;
}

class NetworkTimeoutException extends NetworkException {
  const NetworkTimeoutException() : super('Request timed out');
}

class NetworkConnectionException extends NetworkException {
  const NetworkConnectionException() : super('No internet connection');
}

class UnauthorizedException extends NetworkException {
  const UnauthorizedException(super.message) : super(statusCode: 401);
}

class ApiErrorException extends NetworkException {
  const ApiErrorException(super.message, {super.statusCode});
}

class NetworkUnknownException extends NetworkException {
  const NetworkUnknownException(String? message)
      : super(message ?? 'Something went wrong');
}
