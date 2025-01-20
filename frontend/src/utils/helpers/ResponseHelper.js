export function serialize(res) {
  return {
    data: res?.data,
    status: res?.status || 0,
    headers: res?.headers || null,
  };
}
