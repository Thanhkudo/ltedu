class ApiResponse<T> {
  const ApiResponse({
    required this.data,
    this.message,
    this.statusCode,
  });

  final T data;
  final String? message;
  final int? statusCode;

  factory ApiResponse.fromJson(
    Map<String, dynamic> json, {
    T Function(Map<String, dynamic>)? fromJsonT,
    T Function(List<dynamic>)? fromJsonList,
  }) {
    final rawData = json['data'];
    late T parsed;

    if (fromJsonT != null && rawData is Map<String, dynamic>) {
      parsed = fromJsonT(rawData);
    } else if (fromJsonList != null && rawData is List) {
      parsed = fromJsonList(rawData);
    } else {
      parsed = rawData as T;
    }

    return ApiResponse(
      data: parsed,
      message: json['message']?.toString(),
      statusCode: json['status_code'] as int?,
    );
  }
}
