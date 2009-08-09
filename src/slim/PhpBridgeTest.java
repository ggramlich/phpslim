package slim;

import static org.junit.Assert.*;

import java.io.File;
import java.io.InputStream;
import java.io.InputStreamReader;
import java.lang.reflect.Proxy;

import javax.script.Invocable;
import javax.script.ScriptEngine;
import javax.script.ScriptException;

import org.junit.*;
import static slim.TestSuite.getTestIncludePath;

public class PhpBridgeTest {
  private static PhpBridge bridge;
  
  @BeforeClass
  public static void setUpClass() {
    // Creates Bridge only once
    bridge = new PhpBridge(getTestIncludePath());
  }
  
  @Test
  public void get_internal_php_path() throws Exception {
    File path = new File(bridge.getIncludePath());
    assertTrue(path.exists());
    assertTrue(path.isAbsolute());
    assertTrue(path.isDirectory());
    assertEquals("PhpSlim", path.getName());
  }

  @Test
  public void engine_is_of_correct_type() {
    assertEquals("php-invocable", bridge.getScriptEngine().getFactory().getLanguageName());
  }

  @Test
  public void Simple() throws ScriptException {
    ScriptEngine engine = bridge.getScriptEngine();
    engine.put("x", 4);
    InputStream in = getClass().getResourceAsStream("phplib/addOne.php");
    InputStreamReader reader = new InputStreamReader(in);
    engine.eval(reader);
    assertEquals(5, engine.get("y"));
  }

  @Test
  public void get_php_statement_executor() throws Exception {
    Proxy statementExecutor = bridge.getStatementExecutor();
    assertNotNull(statementExecutor);
    Invocable inv = (Invocable) bridge.getScriptEngine();
    inv.invokeMethod(statementExecutor, "setSymbol", new Object[]{"name", "Bob"});
    Object symbol = inv.invokeMethod(statementExecutor, "getSymbol", new Object[]{"name"});
    assertEquals("Bob", symbol);
  }
  
}
